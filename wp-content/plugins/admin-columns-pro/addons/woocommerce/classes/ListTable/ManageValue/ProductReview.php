<?php

declare(strict_types=1);

namespace ACA\WC\ListTable\ManageValue;

use AC\Table\ManageValue\RenderFactory;
use AC\TableScreen\ManageValueService;
use AC\Type\ColumnId;
use AC\Type\Value;
use WP_Comment;

class ProductReview implements ManageValueService
{
    private const COLUMNS_HOOK = 'manage_product_page_product-reviews_columns';

    private const COLUMN_ACTION_PREFIX = 'woocommerce_product_reviews_table_column_';

    private RenderFactory $factory;

    private int $priority;

    public function __construct(RenderFactory $factory, int $priority = 100)
    {
        $this->factory = $factory;
        $this->priority = $priority;
    }

    public function register(): void
    {
        add_filter(self::COLUMNS_HOOK, [$this, 'register_column_handlers'], PHP_INT_MAX);
    }

    public function render_value(...$args): void
    {
        $item = $args[0] ?? null;

        if (! $item instanceof WP_Comment) {
            return;
        }

        $column_name = substr((string)current_action(), strlen(self::COLUMN_ACTION_PREFIX));

        $formatter = $this->factory->create(new ColumnId($column_name));

        if ($formatter) {
            echo $formatter->format(new Value((int)$item->comment_ID));
        }
    }

    public function register_column_handlers(array $columns): array
    {
        remove_filter(self::COLUMNS_HOOK, [$this, 'register_column_handlers'], PHP_INT_MAX);

        foreach (array_keys($columns) as $column_name) {
            add_action(
                self::COLUMN_ACTION_PREFIX . $column_name,
                [$this, 'render_value'],
                $this->priority
            );
        }

        return $columns;
    }

}
