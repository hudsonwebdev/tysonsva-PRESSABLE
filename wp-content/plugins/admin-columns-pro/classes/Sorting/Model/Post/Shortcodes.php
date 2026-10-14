<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue\ShortCodeCount;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\Order;
use ACP\Sql\QueryBuilder;

class Shortcodes implements WarningAware, QueryBindings
{
    use PostResultsTrait;

    public function __construct()
    {
        $this->formatter = new ShortCodeCount();
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

    public function get_post_ids(): array
    {
        $ids = [];

        foreach ($this->get_query_results() as $row) {
            $ids[$row->id] = $this->formatter
                ? $this->formatter->format_value($row->value)
                : $row->value;
        }

        $ids = array_filter($ids);

        asort($ids);

        return array_keys($ids);
    }

    private function get_query_results(): array
    {
        global $wpdb;

        $query = QueryBuilder::for($wpdb->posts)
            ->select_columns(["$wpdb->posts.ID AS id", "$wpdb->posts.post_content AS value"])
            ->where("$wpdb->posts.post_type", '=', $this->get_var_post_type())
            ->and_where_raw("$wpdb->posts.post_content LIKE '%[%'")
            ->and_where_raw("$wpdb->posts.post_content LIKE '%]%'");

        $status = $this->get_var_post_status();
        if ($status) {
            $query->and_where("$wpdb->posts.post_status", '=', $status);
        }

        return $wpdb->get_results($query->get_sql());
    }

}
