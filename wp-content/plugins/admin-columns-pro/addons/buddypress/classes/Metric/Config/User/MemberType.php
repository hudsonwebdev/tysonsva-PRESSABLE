<?php

declare(strict_types=1);

namespace ACA\BP\Metric\Config\User;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;

class MemberType extends Metric\Config\Base
{
    /**
     * @var array<string, string> [slug => label]
     */
    private array $choices;

    /**
     * @param array<string, string> $choices [slug => label]
     */
    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_choices($this->choices);
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type, 0);
    }

    protected function configure_query_bindings(
        AggregationType $aggregation_type,
        Bindings $bindings,
        string $alias
    ): void {
        global $wpdb;

        $taxonomy_alias = $alias . '_tt';
        $term_alias = $alias . '_t';

        $joins = [
            Join::left($wpdb->term_relationships, $alias)
                 ->on("{$alias}.object_id", '=', "{$wpdb->users}.ID"),
            Join::left($wpdb->term_taxonomy, $taxonomy_alias)
                 ->on("{$taxonomy_alias}.term_taxonomy_id", '=', "{$alias}.term_taxonomy_id")
                 ->on("{$taxonomy_alias}.taxonomy", '=', $wpdb->prepare('%s', 'bp_member_type')),
            Join::left($wpdb->terms, $term_alias)
                 ->on("{$term_alias}.term_id", '=', "{$taxonomy_alias}.term_id"),
        ];

        $bindings->join(implode("\n", array_map('strval', $joins)));
    }

    protected function get_column(string $alias): string
    {
        return $alias . '_t.slug';
    }

}
