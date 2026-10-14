<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactories;

use AC;
use AC\Collection\ColumnFactories;
use ACA\DataSources;
use ACA\DataSources\ColumnFactoryResolver;
use ACA\DataSources\DataSource\Column;
use ACA\DataSources\DataSourceRegistry;
use ACA\DataSources\RelationColumnFactoryResolver;
use ACP\Sql\Alias;

class DataSourceColumns implements AC\ColumnFactoryCollectionFactory
{
    private ColumnFactoryResolver $column_factory_resolver;

    private RelationColumnFactoryResolver $relation_column_factory_resolver;

    private DataSourceRegistry $data_sources;

    public function __construct(
        ColumnFactoryResolver $column_factory_resolver,
        RelationColumnFactoryResolver $relation_column_factory_resolver,
        DataSourceRegistry $data_sources
    ) {
        $this->column_factory_resolver = $column_factory_resolver;
        $this->relation_column_factory_resolver = $relation_column_factory_resolver;
        $this->data_sources = $data_sources;
    }

    public function create(AC\TableScreen $table_screen): ColumnFactories
    {
        $collection = new AC\Collection\ColumnFactories();

        if ($table_screen instanceof DataSources\TableScreen) {
            $data_source = $table_screen->get_data_source();

            foreach ($data_source->get_columns() as $column) {
                $this->add_factory($collection, $column, $data_source);
            }

            foreach ($data_source->get_relations()->get_table_relations() as $relation) {
                $foreign_data_source = $relation->get_foreign_data_source();

                foreach ($foreign_data_source->get_columns() as $column) {
                    $this->add_factory(
                        $collection,
                        $column,
                        $foreign_data_source,
                        $relation->has_alias()
                            ? $relation->get_alias()
                            : null
                    );
                }
            }

            foreach ($data_source->get_relations()->get_column_relations() as $relation) {
                $collection->add(
                    $this->relation_column_factory_resolver->create(
                        $relation,
                        $data_source,
                        $this->data_sources->get_group($data_source->get_id())
                    )
                );
            }
        }

        return $collection;
    }

    private function add_factory(
        AC\Collection\ColumnFactories $collection,
        Column $column,
        DataSources\DataSource $data_source,
        ?Alias $alias = null
    ): void {
        $collection->add(
            $this->column_factory_resolver->create(
                $column->get_name(),
                $column->get_label(),
                $column->get_type(),
                $data_source,
                $this->data_sources->get_group($data_source->get_id()),
                $alias
            )
        );
    }

}
