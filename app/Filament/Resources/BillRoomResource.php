<?php

namespace App\Filament\Resources;

use App\Filament\Imports\BillRoomImporter;
use App\Filament\Resources\BillRoomResource\Pages;
use App\Models\BillRoom;
use App\Models\Room;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\File;

class BillRoomResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = BillRoom::class;

    protected static ?string $slug = 'bill-rooms';

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin chi tiết')
                    ->schema([
                        Select::make('room_id')
                            ->relationship('room', 'name')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state): void {
                                $room = Room::query()->findOrFail($state);
                                $set('price_room', $room->price_room);
                                $set('price_garbage', $room->price_garbage);
                                $set('price_electric', $room->price_electric);
                                $set('price_water', $room->price_water);

                                $lastRecord = BillRoom::latestRecordByAt()->where('room_id', $state)->first();
                                if (! $lastRecord) {
                                    $set('old_electric', 0);
                                    $set('old_water', 0);

                                    return;
                                }

                                $set('old_electric', $lastRecord->new_electric);
                                $set('old_water', $lastRecord->new_water);

                            })
                            ->required(),

                        DatePicker::make('at')->default(now())->label('Time')
                            ->required(),

                        TextInput::make('rent_month')->label('Rent month')->default(function () {
                            return now()->subMonth()->month;
                        })->required(),

                        TextInput::make('new_electric')
                            ->numeric()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state): void {
                                $electric_consumption = $state - $get('old_electric');
                                $set('electric_consumption', $electric_consumption);

                                $set('total_price', self::calculateTotalPrice(
                                    $electric_consumption,
                                    $get('price_electric'),
                                    $get('water_consumption') ?? 0,
                                    $get('price_water'),
                                    $get('price_room'),
                                    $get('price_garbage')
                                ));
                            })
                            ->required(),

                        TextInput::make('electric_consumption')
                            ->numeric()
                            ->required(),

                        TextInput::make('new_water')
                            ->numeric()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state): void {
                                $water_consumption = $state - $get('old_water');
                                $set('water_consumption', $water_consumption);

                                $set('total_price', self::calculateTotalPrice(
                                    $get('electric_consumption'),
                                    $get('price_electric'),
                                    $water_consumption,
                                    $get('price_water'),
                                    $get('price_room'),
                                    $get('price_garbage')
                                ));
                            })
                            ->required(),

                        TextInput::make('water_consumption')
                            ->numeric()
                            ->required(),

                        TextInput::make('total_price')->numeric(),
                        Textarea::make('note'),

                    ])->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make('Thông tin tiền cơ bản')
                            ->schema([
                                TextInput::make('old_electric')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('old_water')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('price_water')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('price_electric')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('price_room')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('price_garbage')
                                    ->numeric()
                                    ->required(),
                            ]),
                        Section::make()
                            ->schema([

                                Placeholder::make('created_at')
                                    ->label('Created Date')
                                    ->content(fn (?BillRoom $record): string => $record?->created_at?->diffForHumans() ?? ' - '),

                                Placeholder::make('updated_at')
                                    ->label('Last Modified Date')
                                    ->content(fn (?BillRoom $record): string => $record?->updated_at?->diffForHumans() ?? ' - '),
                            ])])->columnSpan(['lg' => 1]),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(BillRoomImporter::class)
                    ->visible(fn () => auth()->user()?->can('import_bill::room'))
                    ->fileRules([
                        File::types(['csv'])->max(1024),
                    ]),
            ])
            ->columns([
                TextColumn::make('at')->label('Time')
                    ->date('d/m/Y'),

                TextColumn::make('rent_month')->label('Rent Month')->visibleFrom('md')->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('room.name'),

                TextColumn::make('total_price')->numeric()->money('VND')->summarize(Sum::make()->money('VND')),
                TextColumn::make('old_electric')->visibleFrom('md'),

                TextColumn::make('new_electric')->visibleFrom('md'),

                TextColumn::make('old_water')->visibleFrom('md'),

                TextColumn::make('new_water')->visibleFrom('md'),

                TextColumn::make('electric_consumption')->numeric()->visibleFrom('md')->toggleable(isToggledHiddenByDefault: true)->summarize(Sum::make()),

                TextColumn::make('water_consumption')->numeric()->visibleFrom('md')->toggleable(isToggledHiddenByDefault: true)->summarize(Sum::make()),

                TextColumn::make('price_water')->numeric()->visibleFrom('md')->money('VND')->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('price_electric')->numeric()->visibleFrom('md')->money('VND')->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('price_room')->numeric()->visibleFrom('md')->money('VND')->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('price_garbage')->numeric()->visibleFrom('md')->money('VND')->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('room_id')
                    ->relationship('room', 'name', fn (Builder $query) => $query->withTrashed())
                    ->label('Phòng')
                    ->preload()
                    ->searchable(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Dec 18, '.now()->subYear()->format('Y')),
                        DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Bill from '.Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Bill until '.Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    })->columnSpan(2)->columns(2),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(2)
            ->actions([
                EditAction::make()->iconButton(),
                \Filament\Tables\Actions\Action::make('export-pdf')
                    ->color('success')
                    ->hiddenLabel()
                    ->icon('heroicon-m-document-arrow-down')
                    ->url(fn (Model $record) => route('bill.pdf', $record))
                    ->openUrlInNewTab(),

                DeleteAction::make()->iconButton(),
                RestoreAction::make()->iconButton(),
                ForceDeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    \Filament\Tables\Actions\BulkAction::make('Export pdf')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->openUrlInNewTab()
                        ->action(function (Collection $records, \Livewire\Component $livewire): void {
                            /** @var BillRoom $records */
                            $records->each(function (Model $record) use ($livewire): void {
                                // see this discussion for more information https://github.com/filamentphp/filament/discussions/12309
                                $link = route('bill.pdf', $record);
                                $link = sprintf("window.open('%s', '_blank');", $link);

                                $livewire->js($link);
                            });
                        }),
                ]),
            ])->groups([
                \Filament\Tables\Grouping\Group::make('room.name')->label('room')->titlePrefixedWithLabel(false)->collapsible(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillRooms::route('/'),
            'create' => Pages\CreateBillRoom::route('/create'),
            'edit' => Pages\EditBillRoom::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPermissionPrefixes(): array
    {
        //        merge with key in filament-shield config
        return array_merge(config('filament-shield.permission_prefixes.resource'), [
            'import',
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }

    private static function calculateTotalPrice(float $electric_consumption, float $price_electric, float $water_consumption, float $price_water, float $price_room, float $price_garbage): float
    {
        $elecBill = $electric_consumption * $price_electric;
        return $elecBill + $water_consumption * $price_water + $price_room + $price_garbage;
    }
}
