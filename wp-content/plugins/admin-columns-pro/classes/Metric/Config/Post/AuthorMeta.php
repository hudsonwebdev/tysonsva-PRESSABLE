<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Post;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType;
use ACP\Metric\ColumnType\Existence;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;

class AuthorMeta extends Metric\Config\Base
{
    private string $meta_key;

    private ColumnType $column_type;

    public function __construct(string $meta_key, ?ColumnType $column_type = null)
    {
        $this->meta_key = $meta_key;
        $this->column_type = $column_type ?: new Existence();
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return $this->column_type->get_aggregation_types();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return $this->column_type->create_formatter($aggregation_type);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->usermeta, $alias)
                ->on("{$wpdb->posts}.post_author", '=', "{$alias}.user_id")
                ->on(sprintf('%s.meta_key', $alias), '=', $wpdb->prepare('%s', $this->meta_key))
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return $this->column_type->get_column_expression(
            sprintf('%s.meta_value', $alias)
        );
    }

}
