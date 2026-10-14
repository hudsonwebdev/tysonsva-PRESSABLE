<?php

declare(strict_types=1);

namespace ACA\WC\Search\TableMarkup;

use ACP\Search\TableMarkup;

class ProductReview extends TableMarkup
{
    public function register(): void
    {
        add_filter('woocommerce_product_reviews_list_table', [$this, 'inject_filters_markup']);

        parent::register();
    }

    public function inject_filters_markup(string $html): string
    {
        ob_start();
        $this->filters_markup();
        $markup = ob_get_clean();

        return str_replace(
            '<div class="tablenav top">',
            $markup . '<div class="tablenav top">',
            $html
        );
    }

}
