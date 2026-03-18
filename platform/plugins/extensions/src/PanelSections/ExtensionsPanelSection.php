<?php

namespace Botble\Extensions\PanelSections;

use Botble\Base\PanelSections\PanelSection;

class ExtensionsPanelSection extends PanelSection
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
