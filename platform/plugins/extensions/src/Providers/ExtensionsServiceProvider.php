<?php

namespace Botble\Extensions\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\DashboardMenu;
use Botble\Extensions\Models\Extensions;

class ExtensionsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/extensions')
            ->loadHelpers()
            ->loadAndPublishConfigurations(["permissions"])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations();

            if (defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
                \Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::registerModule(Extensions::class, [
                    'name',
                ]);
            }

            DashboardMenu::default()->beforeRetrieving(function () {
                DashboardMenu::registerItem([
                    'id' => 'cms-plugins-extensions',
                    'priority' => 100,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/extensions::extensions.name',
                    'icon' => 'fa fa-list',
                    'url' => route('extensions.index'),
                    'permissions' => ['extensions.index'],
                ]);
            });
    }
}
