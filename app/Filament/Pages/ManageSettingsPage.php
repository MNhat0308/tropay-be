<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Support\Colors\Color;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ManageSettingsPage extends SettingsPage
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'Site Settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static string $settings = GeneralSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Site Settings')
                            ->schema([

                                Forms\Components\TextInput::make('site_name')
                                    ->maxLength(255),
                                Forms\Components\Select::make('color_theme')
                                    ->options(
                                        Arr::mapWithKeys(Color::all(), function ($color, $key): array {
                                            return [$key => Str::ucfirst($key)];
                                        })
                                    ),
                                Forms\Components\Select::make('font')->label('Font theme')
                                    ->options([
                                        'Playfair Display' => 'Playfair Display',
                                        'Roboto' => 'Roboto',
                                        'Open Sans' => 'Open Sans',
                                        'Noto Sans Japanese' => 'Noto Sans Japanese',
                                        'Montserrat' => 'Montserrat',
                                        'Inter' => 'Inter',
                                        'Roboto Condensed' => 'Roboto Condensed',
                                        'Roboto Mono' => 'Roboto Mono',
                                        'Oswald' => 'Oswald',
                                        'Noto Sans' => 'Noto Sans',
                                        'Raleway' => 'Raleway',
                                        'Nunito' => 'Nunito',
                                        'Nunito Sans' => 'Nunito Sans',
                                        'Noto Sans Korean' => 'Noto Sans Korean',
                                        'Roboto Slab' => 'Roboto Slab', ]
                                    ),
                                Forms\Components\Toggle::make('is_top_nav_enabled')
                                    ->label('Enable top navigation'),
                                Forms\Components\Toggle::make('is_sidebar_collapsible_on_desktop')
                                    ->label('Enable sidebar collapsible on desktop'),

                            ]),
                        Tabs\Tab::make('Tab 2')
                            ->schema([
                            ]),
                    ]),
            ]);
    }
}
