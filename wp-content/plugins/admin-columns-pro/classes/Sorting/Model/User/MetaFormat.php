<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\User;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

/**
 * Sorts a user list table on a meta key. The meta value may contain mixed values, as long
 * as the supplied formatter can process them into a string.
 */
class MetaFormat implements QueryBindings, WarningAware
{
    private string $meta_key;

    private FormatValue $formatter;

    protected DataType $data_type;

    public function __construct(FormatValue $formatter, string $meta_key, ?DataType $data_type = null)
    {
        $this->formatter = $formatter;
        $this->meta_key = $meta_key;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->users.ID",
                $this->get_sorted_ids(),
                (string)$order
            )
        );
    }

    private function get_sorted_ids(): array
    {
        global $wpdb;

        $prepared_meta_key = $wpdb->prepare('%s', $this->meta_key);

        $sql = QueryBuilder::for($wpdb->users, 'uu')
            ->select_columns(['uu.ID AS id', 'um.meta_value AS value'])
            ->left_join($wpdb->usermeta, function (Join $join) use ($prepared_meta_key) {
                $join->on('uu.ID', '=', 'um.user_id')
                     ->on('um.meta_key', '=', $prepared_meta_key)
                     ->on('um.meta_value', '<>', "''");
            }, null, null, 'um')
            ->get_sql();

        $results = $wpdb->get_results($sql);

        if (! $results) {
            return [];
        }

        $values = [];

        foreach ($results as $object) {
            $values[$object->id][] = $this->formatter->format_value($object->value);
        }

        foreach ($values as $id => $meta_values) {
            $values[$id] = trim(implode(' ', $meta_values));
        }

        return (new Sorter())->sort($values, $this->data_type);
    }

}
