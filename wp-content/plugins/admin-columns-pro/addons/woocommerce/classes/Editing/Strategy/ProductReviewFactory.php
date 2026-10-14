<?php

declare(strict_types=1);

namespace ACA\WC\Editing\Strategy;

use AC;
use ACA\WC\TableScreen;
use ACP\Editing\Strategy;
use ACP\Editing\StrategyFactory;

class ProductReviewFactory implements StrategyFactory
{
    public function create(AC\TableScreen $table_screen): ?Strategy
    {
        if (! $table_screen instanceof TableScreen\ProductReview) {
            return null;
        }

        return new ProductReview();
    }

}
