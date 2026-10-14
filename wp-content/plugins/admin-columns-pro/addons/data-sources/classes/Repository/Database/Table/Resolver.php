<?php

declare(strict_types=1);

namespace ACA\DataSources\Repository\Database\Table;

use ACA\DataSources\Exception\MissingIdentifierException;
use ACA\DataSources\Exception\MultipleIdentifierException;
use ACA\DataSources\Exception\TableException;
use ACA\DataSources\Exception\TableNotReadableException;
use ACA\DataSources\Repository\Database\Table;

final class Resolver
{
    /**
     * @throws TableException
     */
    public function resolve(string $table, ?string $identifier = null): Table
    {
        $identifiers = [];
        $columns = [];

        foreach ($this->get_columns($table) as $column) {
            if (
                $identifier === null
                    ? $column->is_primary_key()
                    : $column->get_name() === $identifier
            ) {
                $identifiers[] = $column->get_name();
            }

            $columns[] = $column;
        }

        if (count($identifiers) === 0) {
            throw new MissingIdentifierException($table);
        }

        if (count($identifiers) > 1) {
            throw new MultipleIdentifierException($table);
        }

        return new Table(
            $table,
            $identifiers[0],
            new ColumnCollection($columns)
        );
    }

    /**
     * @throws TableNotReadableException
     */
    private function get_columns(string $table): ColumnCollection
    {
        global $wpdb;

        $database = defined('DB_NAME') ? DB_NAME : '';

        $sql = "
            SELECT
                COLUMN_NAME,
                DATA_TYPE,
                COLUMN_KEY
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = %s
              AND TABLE_NAME = %s
            ORDER BY ORDINAL_POSITION
        ";

        $stmt = $wpdb->prepare($sql, $database, $table);
        $columns = [];

        foreach ((array)$wpdb->get_results($stmt, ARRAY_A) as $row) {
            $columns[] = new Column(
                $row['COLUMN_NAME'],
                $row['DATA_TYPE'],
                $row['COLUMN_KEY']
            );
        }

        // A database table can't exist without a column, something went wrong
        if (! $columns) {
            throw new TableNotReadableException($table);
        }

        return new ColumnCollection($columns);
    }

}
