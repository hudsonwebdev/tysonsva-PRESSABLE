<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class MetaFormat implements QueryBindings
{
    private string $taxonomy;

    private FormatValue $formatter;

    private string $meta_key;

    protected DataType $data_type;

    public function __construct(string $taxonomy, FormatValue $formatter, string $meta_key, ?DataType $data_type = null)
    {
        $this->taxonomy = $taxonomy;
        $this->formatter = $formatter;
        $this->meta_key = $meta_key;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    public function create_query_bindings(Order $order): Bindings
    {
        $bindings = new Bindings();

        $bindings->group_by("t.term_id");
        $bindings->order_by(
            SqlOrderByFactory::create_with_ids(
                "t.term_id",
                $this->get_sorted_ids(),
                (string)$order
            )
        );

        return $bindings;
    }

    private function get_sorted_ids(): array
    {
        global $wpdb;

        $prepared_taxonomy = $wpdb->prepare('%s', $this->taxonomy);
        $prepared_meta_key = $wpdb->prepare('%s', $this->meta_key);

        $sql = QueryBuilder::for($wpdb->terms, 'terms')
            ->select_columns(['terms.term_id AS id', 'tm.meta_value AS value'])
            ->left_join($wpdb->term_taxonomy, function (Join $join) use ($prepared_taxonomy) {
                $join->on('tt.term_id', '=', 'terms.term_id')
                     ->on('tt.taxonomy', '=', $prepared_taxonomy);
            }, null, null, 'tt')
            ->left_join($wpdb->termmeta, function (Join $join) use ($prepared_meta_key) {
                $join->on('tm.term_id', '=', 'terms.term_id')
                     ->on('tm.meta_key', '=', $prepared_meta_key)
                     ->on('tm.meta_value', '<>', "''");
            }, null, null, 'tm')
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
