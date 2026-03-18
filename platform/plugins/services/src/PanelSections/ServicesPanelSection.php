<?php

namespace Botble\Services\PanelSections;

use Botble\Base\PanelSections\PanelSection;

class ServicesPanelSection extends PanelSection
{
    public function setup(): void
    {
        $this
            ->setId('settings.{id}')
            ->setTitle('{title}')
            ->withItems([
                //
            ]);
    }
}
