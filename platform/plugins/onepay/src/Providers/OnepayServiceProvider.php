<?php

namespace Botble\Onepay\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\DashboardMenu;
use Botble\Onepay\Models\Onepay;

class OnepayServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {

        if (!is_plugin_active('payment')) {
            return;
        }
        $this
            ->setNamespace('plugins/onepay')
            ->loadHelpers()
            ->loadAndPublishConfigurations(["permissions"])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations()
            ->publishAssets();

        $this->app->register(HookServiceProvider::class);
    }
}
