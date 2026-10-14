<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\Author;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\Post\PostRequestTrait;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class Roles implements QueryBindings
{
    use PostRequestTrait;

    private FormatValue\Roles $formatter;

    public function __construct()
    {
        $this->formatter = new FormatValue\Roles();
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->posts.ID",
                $this->get_post_ids(),
                (string)$order
            )
        );
    }

    private function get_post_ids(): array
    {
        global $wpdb;

        $query = QueryBuilder::for($wpdb->posts, 'pp')
            ->select_columns(['pp.ID AS id', 'um.meta_value AS caps'])
            ->join($wpdb->usermeta, function (Join $join) {
                $join->on('pp.post_author', '=', 'um.user_id')
                     ->on('um.meta_key', '=', "'wp_capabilities'");
            }, null, null, 'um')
            ->where('pp.post_type', '=', $this->get_var_post_type());

        $status = $this->get_var_post_status();
        if ($status) {
            $query->and_where('pp.post_status', '=', $status);
        }

        $ids = [];

        foreach ($wpdb->get_results($query->get_sql()) as $row) {
            $role = $this->formatter->format_value($row->caps);

            if (! $role) {
                continue;
            }

            $ids[$row->id] = (string)$role;
        }

        natcasesort($ids);

        return array_keys($ids);
    }

}
