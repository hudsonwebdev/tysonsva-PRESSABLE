<?php

declare(strict_types=1);

namespace ACA\BP\Metric\Config\User;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\DateFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;

class LastSeen extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_date();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new DateFormatter($aggregation_type);
    }

    protected function configure_query_bindings(
        AggregationType $aggregation_type,
        Bindings $bindings,
        string $alias
    ): void {
        global $wpdb, $bp;

        $bindings->join(
            Join::left($bp->members->table_name_last_activity, $alias)
                ->on("{$alias}.user_id", '=', "{$wpdb->users}.ID")
                ->on("{$alias}.component", '=', $wpdb->prepare('%s', 'members'))
                ->on("{$alias}.type", '=', $wpdb->prepare('%s', 'last_activity'))
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return sprintf('%s.date_recorded', $alias);
    }

}
