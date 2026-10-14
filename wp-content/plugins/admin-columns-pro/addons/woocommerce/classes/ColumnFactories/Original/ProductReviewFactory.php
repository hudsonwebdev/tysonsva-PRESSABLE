<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactories\Original;

use AC\TableScreen;
use ACA\WC\ColumnFactory\ProductReview\Original;
use ACA\WC\TableScreen\ProductReview;
use ACP\ColumnFactories\Original\OriginalAdvancedColumnFactory;

class ProductReviewFactory extends OriginalAdvancedColumnFactory
{
    protected function get_original_factories(TableScreen $table_screen): array
    {
        if (! $table_screen instanceof ProductReview) {
            return [];
        }

        return [
            'author'   => Original\AuthorFactory::class,
            'comment'  => Original\ReviewFactory::class,
            'response' => Original\ProductFactory::class,
            'date'     => Original\DateFactory::class,
            'rating'   => Original\RatingFactory::class,
            'type'     => Original\TypeFactory::class,
        ];
    }

}
