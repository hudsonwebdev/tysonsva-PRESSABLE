<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Product;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class DaysSinceLastSale extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $sub_query = QueryBuilder::for("{$wpdb->prefix}wc_order_product_lookup", 'opl')
            ->select_columns(['opl.product_id', 'MAX(o.date_created_gmt) AS last_sale_date'])
            ->join("{$wpdb->prefix}wc_orders", 'o.id', '=', 'opl.order_id', 'o')
            ->where('o.status', '=', 'wc-completed')
            ->group_by('opl.product_id')
            ->get_sql();

        $bindings->join(
            Join::left_subquery($sub_query, $alias)
                ->on("$wpdb->posts.ID", '=', "$alias.product_id")
                ->get_sql()
        );

        // "Days since" ASC = most recent first = date DESC, and vice versa
        $reversed = (string)$order === 'ASC' ? 'DESC' : 'ASC';

        $bindings->order_by(
            SqlOrderByFactory::create("{$alias}.last_sale_date", $reversed, [
                'cast_type'    => CastType::create_from_data_type(new DataType(DataType::DATETIME))->get_value(),
                'empty_values' => [null],
            ])
        );
    }

}
