<?php

namespace Botble\Place\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Place\Services\PlaceService;
use Botble\Slug\Models\Slug;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(BASE_FILTER_PUBLIC_SINGLE_DATA, [$this, 'handleSingleView'], 2);

    }

    public function handleSingleView(Slug|array $slug): Slug|array
    {
        return (new PlaceService())->handleFrontRoutes($slug);
    }

}
