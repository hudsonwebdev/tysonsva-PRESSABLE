<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Product;

use ACA\WC\Metric\Formatter\WcPriceFormatter;
use ACP\Metric;
use ACP\Metric\AggregationFactory;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\AliasGenerator;
use ACP\Sql\Expression\Cast;
use ACP\Sql\Expression\Conditional\Coalesce;
use ACP\Sql\Expression\Conditional\NullIf;
use ACP\Sql\Expression\Greatest;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class StockValue implements Metric\Config
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric();
    }

    public function create_query_bindings(AggregationType $aggregation_type): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $pml_alias = AliasGenerator::generate_from_class(static::class);
        $var_alias = AliasGenerator::generate('sv_var');

        $pml_stock_value = new Cast($pml_alias . '.min_price', 'DECIMAL(10,2)')
                           . ' * '
                           . new Cast($pml_alias . '.stock_quantity', 'SIGNED');

        $variation_subquery = QueryBuilder::create()
            ->select('p.post_parent AS parent_id')
            ->add_select(
                sprintf(
                    'SUM(%s) AS total_stock_value',
                    new Cast('pml.min_price', 'DECIMAL(10,2)') . ' * ' . new Cast(
                        'pml.stock_quantity',
                        'SIGNED'
                    )
                )
            )
            ->from($wpdb->posts, 'p')
            ->join($wpdb->prefix . 'wc_product_meta_lookup', 'pml.product_id', '=', 'p.ID', 'pml')
            ->where('p.post_type', '=', 'product_variation')
            ->and_where('p.post_parent', '>', 0)
            ->and_where('pml.stock_quantity', '>', 0)
            ->and_where('pml.min_price', '>', 0)
            ->group_by('p.post_parent');

        $bindings->join(
            Join::left($wpdb->prefix . 'wc_product_meta_lookup', $pml_alias)
                ->on($wpdb->posts . '.ID', '=', $pml_alias . '.product_id')
                ->get_sql()
            . "\n" .
            Join::left_subquery($variation_subquery->get_sql(), $var_alias)
                ->on($wpdb->posts . '.ID', '=', $var_alias . '.parent_id')
                ->get_sql()
        );

        $expression =
            new NullIf(
                new Greatest(
                    new Coalesce($var_alias . '.total_stock_value', $pml_stock_value),
                    '0'
                ),
                '0'
            );

        AggregationFactory::create($aggregation_type)->apply($bindings, (string)$expression);

        return $bindings;
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new WcPriceFormatter($aggregation_type);
    }

}
