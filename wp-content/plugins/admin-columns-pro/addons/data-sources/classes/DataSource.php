<?php

declare(strict_types=1);

namespace ACA\DataSources;

use ACA\DataSources\DataSource\Column;
use ACA\DataSources\DataSource\Columns;
use ACA\DataSources\DataSource\ColumnType;
use ACA\DataSources\DataSource\Config;
use ACA\DataSources\DataSource\RelationFactoryCollection;
use ACA\DataSources\DataSource\Relations;
use ACA\DataSources\Repository\Database;
use ACA\DataSources\Repository\DatabaseFactory;
use ACA\DataSources\Type\DataSourceId;

final class DataSource
{
    private DataSourceId $id;

    private Columns $columns;

    private Relations $relations;

    private Repository $repository;

    public function __construct(
        DataSourceId $id,
        Database\Table $table,
        ?Config\Columns $columns = null,
        ?RelationFactoryCollection $relations = null,
        ?RepositoryFactory $repository_factory = null
    ) {
        // Using a "ポカヨケ": default path automatic, custom path explicit
        $repository_factory = $repository_factory ?? new DatabaseFactory();

        $this->id = $id;
        $this->columns = $this->resolve_columns($table, $columns);
        $this->relations = $this->resolve_relations($table, $relations);
        $this->repository = $repository_factory->create($table);
    }

    public function get_id(): DataSourceId
    {
        return $this->id;
    }

    public function get_columns(): Columns
    {
        return $this->columns;
    }

    public function has_relations(): bool
    {
        return $this->relations->count() > 0;
    }

    public function get_relations(): Relations
    {
        return $this->relations;
    }

    public function get_repository(): Repository
    {
        return $this->repository;
    }

    private function resolve_columns(
        Database\Table $table,
        ?Config\Columns $config = null
    ): Columns {
        $config = $config ?? new Config\Columns();

        // Setup identifier
        $label = $table->get_identifier()->get_name();

        if ($config->has_column_label_resolver()) {
            $label = $config->get_column_label_resolver()->resolve($table->get_identifier());
        }

        if ($config->has_identifier()) {
            $label = $config->get_identifier()->get_label();
        }

        $columns[] = new Column(
            $table->get_identifier()->get_name(),
            $label,
            new ColumnType\IdentifierType(),
            true
        );

        // Setup columns
        foreach ($table->get_columns() as $column) {
            // Skip identifier, needs manual setup
            if ($column->get_name() === $table->get_identifier()->get_name()) {
                continue;
            }

            $type = new ColumnType\ConfigurableType(
                $this->detect_column_type($column, ColumnType\TextType::class)
            );

            $label = $column->get_name();

            if ($config->has_column_label_resolver()) {
                $label = $config->get_column_label_resolver()->resolve($column);
            }

            // Check if there is a config override
            if ($config->has_column($column->get_name())) {
                $column_config = $config->get_column($column->get_name());

                if ($column_config->has_label()) {
                    $label = $column_config->get_label();
                }

                if ($column_config->has_type()) {
                    $type = $column_config->get_type();
                }
            }

            $columns[] = new Column(
                $column->get_name(),
                $label,
                $type,
                true
            );
        }

        return new Columns(
            $table->get_identifier()->get_name(),
            $columns
        );
    }

    private function resolve_relations(
        Database\Table $table,
        ?RelationFactoryCollection $collection = null
    ): Relations {
        $collection = $collection ?? new RelationFactoryCollection();

        $relations = [];

        foreach ($collection as $factory) {
            $relations[] = $factory->create($table);
        }

        return new Relations($relations);
    }

    private function detect_column_type(Database\Table\Column $column, string $default): string
    {
        $data_type = strtolower($column->get_data_type());

        $boolean_data_types = [
            'tinyint(1)',
        ];

        if (in_array($data_type, $boolean_data_types, true)) {
            return ColumnType\BooleanType::class;
        }

        $numeric_data_types = [
            'int',
            'tinyint',
            'smallint',
            'mediumint',
            'bigint',
            'decimal',
            'float',
            'double',
        ];

        foreach ($numeric_data_types as $numeric_data_type) {
            if (str_starts_with($data_type, $numeric_data_type)) {
                return ColumnType\NumberType::class;
            }
        }

        $date_data_types = [
            'date',
        ];

        foreach ($date_data_types as $date_date_type) {
            if (str_starts_with($data_type, $date_date_type)) {
                return ColumnType\DateTimeType::class;
            }
        }

        return $default;
    }

}
