<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\User;

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

        $status_filter = '';
        if ($this->status) {
            $status_filter = " AND " . Comparison::in('o.status', $this->status);
        }

        $aggregate_function = 'min' === $this->extrema ? 'MIN' : 'MAX';

        $sub_query = QueryBuilder::create()
            ->select(sprintf('u.ID, %s(o.date_created_gmt) AS orderdate', $aggregate_function))
            ->from($wpdb->users, 'u')
            ->join_raw(sprintf('LEFT JOIN %swc_orders as o ON o.customer_id = u.ID%s', $wpdb->prefix, $status_filter))
            ->group_by('u.ID');

        $bindings->join(
            Join::left_subquery($sub_query->get_sql(), $alias)
                ->on($wpdb->users . '.ID', '=', $alias . '.ID')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return sprintf('%s.orderdate', $alias);
    }

}
