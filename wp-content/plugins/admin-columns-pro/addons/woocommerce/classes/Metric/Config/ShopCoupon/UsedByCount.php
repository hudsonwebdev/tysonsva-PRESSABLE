<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\ShopCoupon;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class UsedByCount extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric()->with_existence();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $sub_query = QueryBuilder::create()
            ->select('post_id')
            ->add_select('COUNT(DISTINCT meta_value) AS customer_count')
            ->from($wpdb->postmeta)
            ->where('meta_key', '=', '_used_by')
            ->group_by('post_id');

        $bindings->join(
            Join::left_subquery($sub_query->get_sql(), $alias)
                ->on($wpdb->posts . '.ID', '=', $alias . '.post_id')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return $alias . '.customer_count';
    }

}
