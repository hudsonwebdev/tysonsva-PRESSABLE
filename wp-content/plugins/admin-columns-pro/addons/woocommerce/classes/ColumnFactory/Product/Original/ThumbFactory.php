<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Product\Original;

use AC\Setting\Config;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Search\Comparison\Post\FeaturedImage;

class ThumbFactory extends OriginalColumnFactory
{
    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Post\FeaturedImage();
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new FeaturedImage('product');
    }

}
