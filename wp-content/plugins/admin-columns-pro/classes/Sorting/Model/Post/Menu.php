<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use AC\Helper;
use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class Menu implements QueryBindings
{
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

        $sql = QueryBuilder::for($wpdb->posts, 'p')
            ->select_columns(['p.ID AS id', 'menu.ID AS menu_item_id'])
            ->join($wpdb->postmeta, function (Join $join) {
                $join->on('pm1.meta_value', '=', 'p.ID')
                     ->on('pm1.meta_key', '=', "'_menu_item_object_id'");
            }, null, null, 'pm1')
            ->join($wpdb->posts, function (Join $join) {
                $join->on('menu.ID', '=', 'pm1.post_id')
                     ->on('menu.post_type', '=', "'nav_menu_item'");
            }, null, null, 'menu')
            ->join($wpdb->postmeta, function (Join $join) {
                $join->on('pm2.post_id', '=', 'menu.ID')
                     ->on('pm2.meta_key', '=', "'_menu_item_type'")
                     ->on('pm2.meta_value', '=', "'post_type'");
            }, null, null, 'pm2')
            ->where('p.post_type', '=', $this->post_type)
            ->get_sql();

        $values = [];

        foreach ($wpdb->get_results($sql) as $object) {
            $values[$object->id][] = $this->get_menu_label((int)$object->menu_item_id);
        }

        // natural sort multiple assigned menu's
        foreach ($values as $id => $_values) {
            natcasesort($_values);

            $values[$id] = implode(' ', $_values);
        }

        return (new Sorter())->sort($values);
    }

    private function get_menu_label(int $menu_item_id): string
    {
        static $menu_labels = [];

        if (! isset($menu_labels[$menu_item_id])) {
            $menu_labels[$menu_item_id] = Helper\Menu::create()->get_label($menu_item_id);
        }

        return $menu_labels[$menu_item_id];
    }

}
