<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $slug = 'tenants';

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('room_id')
                            ->relationship('room', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->required(),

                        DatePicker::make('dob'),

                        TextInput::make('address'),

                        TextInput::make('identification'),

                        TextInput::make('gender'),

                        DatePicker::make('start'),

                        DatePicker::make('end'),

                        TextInput::make('note'),

                        TextInput::make('phone'),

                        KeyValue::make('addition_information'),

                        CuratorPicker::make('files')
                            ->constrained(true)
                            ->multiple(),

                    ])->columnSpan(3),
                Group::make()
                    ->schema([

                        Section::make()
                            ->schema([
                                TextInput::make('status'),
                            ]),

                        Section::make()
                            ->schema([
                                Placeholder::make('created_at')
                                    ->label('Created Date')
                                    ->content(fn (?Tenant $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                                Placeholder::make('updated_at')
                                    ->label('Last Modified Date')
                                    ->content(fn (?Tenant $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                            ])
                            ->visible(fn ($livewire): bool => $livewire instanceof Pages\EditTenant),

                    ])->columnSpan(1),

            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('room.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('dob')
                    ->date(),

                TextColumn::make('address'),

                TextColumn::make('identification'),

                TextColumn::make('gender'),

                TextColumn::make('addition_information'),

                TextColumn::make('start')
                    ->date(),

                TextColumn::make('end')
                    ->date(),

                TextColumn::make('status'),

                TextColumn::make('note'),

                TextColumn::make('phone'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    //    protected static function getGlobalSearchEloquentQuery(): Builder
    //    {
    //        return parent::getGlobalSearchEloquentQuery()->with(['room']);
    //    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'room.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->room) {
            $details['Room'] = $record->room->name;
        }

        return $details;
    }
}
