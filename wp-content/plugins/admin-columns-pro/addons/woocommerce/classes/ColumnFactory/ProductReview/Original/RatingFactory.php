<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ProductReview\Original;

use AC\Formatter\Comment\Meta;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC\Metric\Config\ProductReview\Rating;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;
use ACP\Search\Comparison;
use ACP\Search\Comparison\Meta\Number;

class RatingFactory extends OriginalColumnFactory
{
    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(
            new Meta('rating')
        );
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new Rating();
    }

    protected function get_search(Config $config): ?Comparison
    {
        return new Number('rating');
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        $rating = array_map('strval', range(1, 5));

        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Select(
                array_combine($rating, $rating),
            ),
            new ACP\Editing\Storage\Comment\Meta('rating')
        );
    }

}
