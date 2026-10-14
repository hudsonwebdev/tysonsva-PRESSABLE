<?php

declare(strict_types=1);

namespace ACA\WC\TableScreen;

use AC;
use AC\ListTableFactory;
use AC\TableScreen;
use AC\Type\Labels;
use AC\Type\TableId;
use AC\Type\Url\AdminUrl;
use WP_List_Table;

class ProductReview extends TableScreen implements TableScreen\ListTable, TableScreen\MetaType, TableScreen\TotalItems
{
    private int $total_items = 0;

    public function __construct()
    {
        parent::__construct(
            new TableId('wc_product_review'),
            'product_page_product-reviews',
            new Labels(
                __('Review', 'woocommerce'),
                __('Reviews', 'woocommerce')
            ),
            new AdminUrl('edit.php?post_type=product&page=product-reviews'),
            '#the-comment-list'
        );

        add_filter('woocommerce_product_reviews_list_table', [$this, 'capture_total_items'], 10, 2);
    }

    public function capture_total_items(string $html, WP_List_Table $list_table): string
    {
        $this->total_items = (int)($list_table->get_pagination_arg('total_items') ?? 0);

        return $html;
    }

    public function get_total_items(): int
    {
        return $this->total_items;
    }

    public function list_table(): AC\ListTable
    {
        return ListTableFactory::create_comment($this->screen_id);
    }

    public function get_meta_type(): AC\MetaType
    {
        return AC\MetaType::create_comment_meta();
    }

}
