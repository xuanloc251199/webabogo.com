<?php

namespace Botble\Place\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\DashboardMenu;
use Botble\Place\Models\Place;
use Botble\Slug\Facades\SlugHelper;

class PlaceServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        SlugHelper::registerModule(Place::class, 'Place');

        SlugHelper::setPrefix(Place::class, 'place', true);
        $this
            ->setNamespace('plugins/place')
            ->loadHelpers()
            ->loadAndPublishConfigurations(["permissions"])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations();

        if (defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            \Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::registerModule(Place::class, [
                'name',
                'description',
            ]);
        }
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
        DashboardMenu::default()->beforeRetrieving(function () {
            DashboardMenu::registerItem([
                'id' => 'cms-plugins-place',
                'priority' => 5,
                'parent_id' => null,
                'name' => 'plugins/place::place.name',
                'icon' => 'fa fa-list',
                'url' => route('place.index'),
                'permissions' => ['place.index'],
            ]);
        });
    }
}
