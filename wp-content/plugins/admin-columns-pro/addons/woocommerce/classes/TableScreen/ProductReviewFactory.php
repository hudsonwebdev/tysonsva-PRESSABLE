<?php

declare(strict_types=1);

namespace ACA\WC\TableScreen;

use AC\TableScreen;
use AC\TableScreenFactory;
use AC\Type\TableId;
use WP_Screen;

class ProductReviewFactory implements TableScreenFactory
{
    public function create(TableId $id): TableScreen
    {
        return new ProductReview();
    }

    public function can_create(TableId $id): bool
    {
        return $id->equals(new TableId('wc_product_review'));
    }

    public function create_from_wp_screen(WP_Screen $screen): TableScreen
    {
        return new ProductReview();
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return 'product_page_product-reviews' === $screen->base;
    }

}
