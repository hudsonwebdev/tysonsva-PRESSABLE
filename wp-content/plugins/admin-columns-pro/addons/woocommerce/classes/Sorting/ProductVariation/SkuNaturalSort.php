<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\ProductVariation;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Post\PostRequestTrait;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class SkuNaturalSort implements QueryBindings
{
    use PostRequestTrait;

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->posts.ID",
                $this->get_sorted_ids(),
                (string)$order
            )
        );
    }

    private function get_sorted_ids(): array
    {
        global $wpdb;

        $query = QueryBuilder::for($wpdb->posts, 'pp')
            ->select_columns([
                'pp.ID AS id',
                "COALESCE(NULLIF(acsort_postmeta.meta_value, ''), acsort_parentmeta.meta_value) AS sku",
            ])
            ->join($wpdb->posts, function (Join $join) {
                $join->on('acsort_parent.ID', '=', 'pp.post_parent')
                     ->on("acsort_parent.post_type", '=', "'product'");
            }, null, null, 'acsort_parent')
            ->left_join($wpdb->postmeta, function (Join $join) {
                $join->on('acsort_postmeta.post_id', '=', 'pp.ID')
                     ->on("acsort_postmeta.meta_key", '=', "'_sku'");
            }, null, null, 'acsort_postmeta')
            ->left_join($wpdb->postmeta, function (Join $join) {
                $join->on('acsort_parentmeta.post_id', '=', 'acsort_parent.ID')
                     ->on("acsort_parentmeta.meta_key", '=', "'_sku'");
            }, null, null, 'acsort_parentmeta')
            ->where('pp.post_type', '=', 'product_variation');

        $status = $this->get_var_post_status();

        if ($status) {
            $query->and_where('pp.post_status', '=', $status);
        }

        $results = $wpdb->get_results($query->get_sql());

        if (empty($results)) {
            return [];
        }

        $ids = [];

        foreach ($results as $object) {
            if ($object->sku) {
                $ids[$object->id] = $object->sku;
            }
        }

        natcasesort($ids);

        return array_keys($ids);
    }

}
