<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Product\Original;

use AC\Setting\Config;
use ACA\WC\Editing;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;

class SkuFactory extends OriginalColumnFactory
{
    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Text())->set_clear_button(true),
            new Editing\Storage\Product\Sku()
        );
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Meta\Text('_sku');
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new ACP\Sorting\Model\Post\Meta('_sku');
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Text::for_post('_sku');
    }

}
