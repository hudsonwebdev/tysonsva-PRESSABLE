<?php

declare(strict_types=1);

namespace ACA\WC\Search\Query;

use AC\TableScreen;
use ACA\WC\TableScreen\ProductReview;
use ACP\Query;
use ACP\Query\QueryFactory;
use ACP\Query\Type\Comment;

class ProductReviewFactory implements QueryFactory
{
    public function can_create(TableScreen $table_screen): bool
    {
        return $table_screen instanceof ProductReview;
    }

    public function create(array $bindings): Query
    {
        return new Comment($bindings);
    }

}
