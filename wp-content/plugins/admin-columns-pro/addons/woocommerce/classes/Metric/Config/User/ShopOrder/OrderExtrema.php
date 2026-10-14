<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\User\ShopOrder;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\DateFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Comparison;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;
use InvalidArgumentException;

class OrderExtrema extends Metric\Config\Base
{
    private string $extrema;

    private array $status;

    public function __construct(string $extrema = 'min', array $status = ['wc-completed'])
    {
        $this->extrema = $extrema;
        $this->status = $status;
        $this->validate();
    }

    private function validate(): void
    {
        if (! in_array($this->extrema, ['min', 'max'], true)) {
            throw new InvalidArgumentException('Invalid extrema');
        }
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new DateFormatter($aggregation_type, 'Y-m-d H:i:s');
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_date();
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $aggregate_function = 'min' === $this->extrema ? 'MIN' : 'MAX';

        $sub_query = QueryBuilder::create()
            ->select('pm_customer.meta_value AS user_id')
            ->add_select(sprintf('%s(o.post_date_gmt) AS orderdate', $aggregate_function))
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
                ->on($wpdb->users . '.ID', '=', $alias . '.user_id')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return sprintf('%s.orderdate', $alias);
    }

}
