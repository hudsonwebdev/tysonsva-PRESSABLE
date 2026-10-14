<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\User\ShopOrder;

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

        $sub_query = QueryBuilder::create()
            ->select('pm_customer.meta_value AS user_id')
            ->add_select('COUNT(o.ID) AS ordercount')
            ->from($wpdb->posts, 'o')
            ->join_raw(sprintf(
                "INNER JOIN %s AS pm_customer ON pm_customer.post_id = o.ID AND pm_customer.meta_key = '_customer_user'",
                $wpdb->postmeta
            ))
            ->where('o.post_type', '=', 'shop_order')
            ->group_by('pm_customer.meta_value');

        if ($this->status) {
            $sub_query->and_where_raw((string)Comparison::in('o.post_status', $this->status));
        }

        $bindings->join(
            Join::left_subquery($sub_query->get_sql(), $alias)
                ->on("{$wpdb->users}.ID", '=', "{$alias}.user_id")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Coalesce(new Cast($alias . '.ordercount', 'DECIMAL(10,2)'), '0');
    }

}
