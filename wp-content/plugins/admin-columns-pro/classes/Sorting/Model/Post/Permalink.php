<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\Order;
use ACP\Sql\QueryBuilder;

class Permalink implements WarningAware, QueryBindings
{
    use PostRequestTrait;

    private string $post_type;

    public function __construct(string $post_type)
    {
        $this->post_type = $post_type;
    }

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

        // only fetch the fields needed for `get_permalink()`
        $query = QueryBuilder::for($wpdb->posts, 'pp')
            ->select_columns(['pp.ID', 'pp.post_type', 'pp.post_status', 'pp.post_name', 'pp.post_date', 'pp.post_parent'])
            ->where('pp.post_type', '=', $this->post_type)
            ->and_where_raw("pp.post_name <> ''");

        $status = $this->get_var_post_status();
        if ($status) {
            $query->and_where('pp.post_status', '=', $status);
        }

        $results = $wpdb->get_results($query->get_sql());

        if (! $results) {
            return [];
        }

        $values = [];

        foreach ($results as $object) {
            $post = get_post($object);

            if (null === $post) {
                continue;
            }

            $link = get_permalink($post);

            if ($link && is_string($link)) {
                $values[$object->ID] = $link;
            }
        }

        natcasesort($values);

        return array_keys($values);
    }

}
