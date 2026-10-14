<?php

declare(strict_types=1);

namespace ACA\BP\Metric\Config\User;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Conditional\Coalesce;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class Groups extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

    protected function configure_query_bindings(
        AggregationType $aggregation_type,
        Bindings $bindings,
        string $alias
    ): void {
        global $wpdb, $bp;

        $subquery = QueryBuilder::for($bp->groups->table_name_members)
            ->select_columns(['user_id', 'COUNT(*) AS total'])
            ->where('is_confirmed', '=', 1)
            ->and_where('is_banned', '=', 0)
            ->group_by('user_id');

        $bindings->join(
            Join::left_subquery($subquery->get_sql(), $alias)
                ->on("{$alias}.user_id", '=', "{$wpdb->users}.ID")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Coalesce("{$alias}.total", '0');
    }

}
