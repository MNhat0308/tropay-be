<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'My Site');
        $this->migrator->add('general.color_theme', 'sky');
        $this->migrator->add('general.font', 'Roboto');
        $this->migrator->add('general.is_top_nav_enabled', 'false');
        $this->migrator->add('general.is_sidebar_collapsible_on_desktop', 'false');

    }
};
