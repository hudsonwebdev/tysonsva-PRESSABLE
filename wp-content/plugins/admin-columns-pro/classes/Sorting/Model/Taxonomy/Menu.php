<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Taxonomy;

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
    private string $taxonomy;

    public function __construct(string $taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "t.term_id",
                $this->get_sorted_ids(),
                (string)$order
            )
        );
    }

    private function get_sorted_ids(): array
    {
        global $wpdb;

        $prepared_taxonomy = $wpdb->prepare('%s', $this->taxonomy);

        $sql = QueryBuilder::for($wpdb->terms, 't')
            ->select_columns(['t.term_id AS id', 'menu.ID AS menu_id'])
            ->join($wpdb->term_taxonomy, function (Join $join) use ($prepared_taxonomy) {
                $join->on('tt.term_id', '=', 't.term_id')
                     ->on('tt.taxonomy', '=', $prepared_taxonomy);
            }, null, null, 'tt')
            ->join($wpdb->postmeta, function (Join $join) {
                $join->on('pm1.meta_value', '=', 't.term_id')
                     ->on('pm1.meta_key', '=', "'_menu_item_object_id'");
            }, null, null, 'pm1')
            ->join($wpdb->posts, function (Join $join) {
                $join->on('menu.ID', '=', 'pm1.post_id')
                     ->on('menu.post_type', '=', "'nav_menu_item'");
            }, null, null, 'menu')
            ->join($wpdb->postmeta, function (Join $join) {
                $join->on('pm2.post_id', '=', 'menu.ID')
                     ->on('pm2.meta_key', '=', "'_menu_item_type'")
                     ->on('pm2.meta_value', '=', "'taxonomy'");
            }, null, null, 'pm2')
            ->get_sql();

        $values = [];

        foreach ($wpdb->get_results($sql) as $object) {
            $menu_id = (int)$object->menu_id;

            if ($menu_id > 0) {
                $values[$object->id][] = $this->get_menu_label((int)$object->menu_id);
            }
        }

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
