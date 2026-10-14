<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ProductReview\Original;

use AC\Formatter\Comment\Property;
use AC\Formatter\Post\PostTitle;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACP\Column\OriginalColumnFactory;

class ProductFactory extends OriginalColumnFactory
{
    protected function get_export(Config $config): ?FormatterCollection
    {
        return new FormatterCollection([
            new Property('comment_post_ID'),
            new PostTitle(),
        ]);
    }

}
