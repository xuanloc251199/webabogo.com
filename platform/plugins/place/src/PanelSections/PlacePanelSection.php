<?php

namespace Botble\Place\PanelSections;

use Botble\Base\PanelSections\PanelSection;

class PlacePanelSection extends PanelSection
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
