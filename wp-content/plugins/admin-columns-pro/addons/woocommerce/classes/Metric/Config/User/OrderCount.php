<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\User;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Cast;
use ACP\Sql\Expression\Comparison;
use ACP\Sql\Expression\Conditional\Coalesce;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class OrderCount extends Metric\Config\Base
{
    private array $status;

    public function __construct(array $status = [])
    {
        $this->status = $status;
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric();
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $status_filter = '';
        if ($this->status) {
            $status_filter = " AND " . Comparison::in('o.status', $this->status);
        }

        $sub_query = QueryBuilder::create()
            ->select('u.ID, COUNT(o.id) AS ordercount')
            ->from($wpdb->users, 'u')
            ->join_raw(sprintf('LEFT JOIN %swc_orders as o ON o.customer_id = u.ID%s', $wpdb->prefix, $status_filter))
            ->group_by('u.ID')
            ->get_sql();

        $bindings->join(
            Join::left_subquery($sub_query, $alias)
                ->on("{$wpdb->users}.ID", '=', "{$alias}.ID")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Coalesce(new Cast($alias . '.ordercount', 'DECIMAL(10,2)'), '0');
    }

}
