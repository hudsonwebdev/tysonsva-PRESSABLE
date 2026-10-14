<?php

declare(strict_types=1);

namespace ACA\DataSources;

use AC;
use AC\ColumnCollection;
use ACA\DataSources;

final class DefaultColumnHandler implements AC\DefaultColumnHandler
{
    private ColumnFactories\DataSourceColumns $column_factories;

    public function __construct(ColumnFactories\DataSourceColumns $column_factories)
    {
        $this->column_factories = $column_factories;
    }

    public function handle(AC\TableScreen $table_screen, ColumnCollection $default_columns): ColumnCollection
    {
        if ($table_screen instanceof DataSources\TableScreen) {
            $factories = $this->column_factories->create($table_screen);
            $data_source = $table_screen->get_data_source();

            $this->add_default_columns(
                $default_columns,
                $data_source,
                $factories,
            );

            foreach ($data_source->get_relations()->get_table_relations() as $relation) {
                $this->add_default_columns(
                    $default_columns,
                    $relation->get_foreign_data_source(),
                    $factories
                );
            }
        }

        return $default_columns;
    }

    private function add_default_columns(
        ColumnCollection $default_columns,
        DataSource $data_source,
        AC\Collection\ColumnFactories $factories
    ): void {
        $identifier = $data_source->get_columns()->get_identifier();

        foreach ($data_source->get_columns() as $column) {
            // Identifier is added as an original
            if ($identifier->equals($column)) {
                continue;
            }

            // Skip columns that are not marked as default columns
            if (! $column->is_default_column()) {
                continue;
            }

            foreach ($factories as $factory) {
                if (! $factory instanceof ColumnFactory || $column->get_name() !== $factory->get_id()) {
                    continue;
                }

                $default_columns->add(
                    $factory->create(new AC\Setting\Config())
                );
            }
        }
    }

}
