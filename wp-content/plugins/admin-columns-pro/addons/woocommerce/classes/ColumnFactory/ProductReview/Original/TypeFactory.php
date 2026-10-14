<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ProductReview\Original;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC\Value\Formatter\ProductReview\ReviewType;
use ACP\Column\OriginalColumnFactory;

class TypeFactory extends OriginalColumnFactory
{
    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new ReviewType());
    }

}
