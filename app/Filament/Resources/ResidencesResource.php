<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResidencesResource\Pages;
use App\Models\Residences;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DatePicker;
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

class ResidencesResource extends Resource
{
    protected static ?string $model = Residences::class;

    protected static ?string $slug = 'residences';

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema(
                        [
                            Select::make('tenant_id')
                                ->relationship('tenant', 'name')
                                ->searchable()
                                ->required(),

                            DatePicker::make('at'),

                            TextInput::make('lookup_code'),

                            Select::make('status_id')
                                ->relationship('status', 'name')
                                ->preload(),

                            KeyValue::make('addition_information'),

                            CuratorPicker::make('files')
                                ->multiple(),

                        ]
                    )->columnSpan(3),
                Section::make()
                    ->heading('Additional Information')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created Date')
                            ->content(fn (?Residences $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Placeholder::make('updated_at')
                            ->label('Last Modified Date')
                            ->content(fn (?Residences $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ])
                    ->visible(fn ($livewire): bool => $livewire instanceof Pages\CreateResidences)
                    ->columnSpan(1),

            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('at')
                    ->date(),

                TextColumn::make('lookup_code'),

                TextColumn::make('status.name')->badge()->color('primary'),

                TextColumn::make('addition_information'),
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
            'index' => Pages\ListResidences::route('/'),
            'create' => Pages\CreateResidences::route('/create'),
            'edit' => Pages\EditResidences::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['tenant.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->tenant) {
            $details['Tenant'] = $record->tenant->name;
        }

        return $details;
    }
}
