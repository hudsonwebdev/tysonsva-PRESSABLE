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

class AuthorField extends Metric\Config\Base
{
    private string $user_field;

    private ColumnType $column_type;

    public function __construct(string $user_field, ?ColumnType $column_type = null)
    {
        $this->user_field = $user_field;
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
            Join::left($wpdb->users, $alias)
                ->on("{$wpdb->posts}.post_author", '=', "{$alias}.ID")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return $this->column_type->get_column_expression(
            sprintf('%s.%s', $alias, $this->user_field)
        );
    }

}
