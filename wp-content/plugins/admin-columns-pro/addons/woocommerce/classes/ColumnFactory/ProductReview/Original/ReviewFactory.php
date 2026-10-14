<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ProductReview\Original;

use AC\Formatter\Comment\CommentText;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACP;
use ACP\Column\OriginalColumnFactory;

class ReviewFactory extends OriginalColumnFactory
{
    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new CommentText());
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Comment\Content();
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Comment\Content();
    }

}
