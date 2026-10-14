<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Formatter\Color;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\DataSources\Editing;
use ACP;

class ColorFactory extends StringFactory
{
    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)
            ->add(new Color());
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Color())->set_clear_button(true),
            new Editing\Storage\Column($this->data_source, $this->id)
        );
    }

}
