<?php

namespace Botble\Onepay\PanelSections;

use Botble\Base\PanelSections\PanelSection;

class OnepayPanelSection extends PanelSection
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
