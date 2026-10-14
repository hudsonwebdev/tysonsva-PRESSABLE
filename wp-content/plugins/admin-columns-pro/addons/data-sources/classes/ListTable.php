<?php

declare(strict_types=1);

namespace ACA\DataSources;

use AC;
use AC\Type\ColumnId;
use AC\Type\Value;
use ACA\DataSources\DataSource\Column;
use ACA\DataSources\DataSource\Relation;
use ACA\DataSources\Sql\RelationJoin;
use ACP\Query\Bindings;
use WP_List_Table;

final class ListTable extends WP_List_Table implements AC\ListTable
{
    private DataSource $data_source;

    private Column $identifier;

    private string $slug;

    public function __construct(DataSource $data_source, string $slug, string $screen_id)
    {
        parent::__construct([
            'ajax'   => false,
            'screen' => $screen_id,
        ]);

        $this->data_source = $data_source;
        $this->identifier = $data_source->get_columns()->get_identifier();
        $this->slug = $slug;
    }

    public function get_columns(): array
    {
        return [
            'cb'                          => '<input type="checkbox">',
            $this->identifier->get_name() => $this->identifier->get_label(),
        ];
    }

    public function render_row($id): string
    {
        $item = $this->data_source->get_repository()->find($id);

        if (! $item) {
            return '';
        }

        ob_start();

        $this->single_row($item);

        return (string)ob_get_clean();
    }

    public function render_cell(string $column_id, $row_id): ?string
    {
        $item = $this->data_source->get_repository()->find($row_id);

        if (! $item) {
            return null;
        }

        return $this->column_default($item, $column_id);
    }

    public function column_default($item, $column_name): string
    {
        // Identifier is also a default column, rest is managed by AC
        if ($column_name === $this->identifier->get_name()) {
            return $item[$this->identifier->get_name()];
        }

        $value = new Value(
            $item[$this->identifier->get_name()],
            $item
        );

        $value = apply_filters(
            'manage_data_source_column_value',
            $value,
            new ColumnId($column_name)
        );

        return (string)$value;
    }

    private function print_hidden_form_fields(): void
    {
        echo '<input type="hidden" name="page" value="' . esc_attr($this->slug) . '">';
    }

    public function display(): void
    {
        $this->print_hidden_form_fields();
        $this->views();

        do_action('acp/data-sources/before_table');

        parent::display();
    }

    protected function extra_tablenav($which): void
    {
        echo '<div class="actions acp-filtering">';

        if ('top' === $which) {
            ob_start();

            do_action('acp/data-sources/restrict_items', $which);

            $output = ob_get_clean();

            if (! empty($output)) {
                echo $output;

                submit_button(
                    __('Filter', 'codepress-admin-columns'),
                    '',
                    'filter_action',
                    false,
                    ['id' => Addon::slug('query-submit')]
                );
            }
        }

        echo '</div>';
    }

    public function column_cb($item): string
    {
        return sprintf(
            '<input type="checkbox" name="item[]" value="%d">',
            $item[$this->identifier->get_name()]
        );
    }

    /**
     * @param string[] $columns
     */
    private function get_relation_select_columns(Relation $relation, array $columns): array
    {
        $select = [];

        foreach ($columns as $column) {
            if ($relation->has_alias()) {
                $select[] = sprintf(
                    '`%1$s`.`%2$s` AS `%1$s_%2$s`',
                    $relation->get_alias(),
                    $column,
                );
            } else {
                $select[] = sprintf(
                    '`%s`.`%s`',
                    $relation->get_foreign_data_source()->get_repository()->get_table(),
                    $column
                );
            }
        }

        return $select;
    }

    public function prepare_items(): void
    {
        $per_page = $this->get_items_per_page($this->data_source->get_id() . '_per_page');
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        $repository = $this->data_source->get_repository();

        $query = $repository
            ->query()
            ->select_all_columns()
            ->limit($per_page, $offset)
            ->order_by_identifier();

        /** @var Bindings[] $collection */
        $collection = (array)apply_filters('acp/data-sources/bindings', []);

        foreach ($collection as $bindings) {
            if ($bindings->get_where()) {
                $query->and_where_raw($bindings->get_where());
            }

            if ($bindings->get_join()) {
                $query->join_raw($bindings->get_join());
            }

            if ($bindings->get_order_by()) {
                $query->order_by($bindings->get_order_by());
            }
        }

        foreach ($this->data_source->get_relations()->get_table_relations() as $relation) {
            $columns = [];

            foreach ($relation->get_foreign_data_source()->get_columns() as $column) {
                $columns[] = $column->get_name();
            }

            $query
                ->add_select_columns($this->get_relation_select_columns($relation, $columns))
                ->join_raw(RelationJoin::from_relation($relation)->get_sql());
        }

        // Make sure joins on 1-N columns return only a single item
        $query->group_by_column(
            sprintf('`%s`.`%s`', $repository->get_table(), $repository->get_identifier())
        );

        $query = apply_filters('acp/data-sources/pre_get_items', $query);

        $this->items = $query->all();
        $total_items = $query->count();

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);
    }

}
