<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public string $color_theme;

    public string $font;

    public bool $is_top_nav_enabled;

    public bool $is_sidebar_collapsible_on_desktop;

    public static function group(): string
    {
        return 'general';
    }
}
