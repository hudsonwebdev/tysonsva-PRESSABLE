<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ProductReview\Original;

use AC\Formatter\Comment\Author;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACP\Column\OriginalColumnFactory;

class AuthorFactory extends OriginalColumnFactory
{
    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new Author());
    }

}
