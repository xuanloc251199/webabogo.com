<?php

namespace Botble\Services\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\DashboardMenu;
use Botble\Services\Models\Services;

class ServicesServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/services')
            ->loadHelpers()
            ->loadAndPublishConfigurations(["permissions"])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations();

            if (defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
                \Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::registerModule(Services::class, [
                    'name',
                ]);
            }

            DashboardMenu::default()->beforeRetrieving(function () {
                DashboardMenu::registerItem([
                    'id' => 'cms-plugins-services',
                    'priority' => 100,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'Dịch vụ đi kèm',
                    'icon' => 'fa fa-list',
                    'url' => route('services.index'),
                    'permissions' => ['services.index'],
                ]);
            });
    }
}
