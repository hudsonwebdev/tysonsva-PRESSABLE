<?php

declare(strict_types=1);

namespace ACA\DataSources\Repository;

use ACA\DataSources\Exception\FailedToDeleteRowException;
use ACA\DataSources\Repository;
use ACA\DataSources\Repository\Database\Table;
use ACA\DataSources\Sql\TableAwareQueryBuilder;
use ACP\Exception\FailedToEditValue;
use ACP\Sql\Identifier;

class Database implements Repository
{
    private Table $table;

    public function __construct(
        Table $table
    ) {
        $this->table = $table;
    }

    public function get_table(): string
    {
        return $this->table->get_name();
    }

    public function get_identifier(): string
    {
        return $this->table->get_identifier()->get_name();
    }

    public function query(): TableAwareQueryBuilder
    {
        return new TableAwareQueryBuilder($this->table);
    }

    public function find($id): array
    {
        return $this
            ->query()
            ->select_all_columns()
            ->where_identifier($id)
            ->one();
    }

    public function get_value($id, string $column): ?string
    {
        $row = $this
            ->query()
            ->select(Identifier::quote($column))
            ->where_identifier($id)
            ->one();

        return $row[$column] ?? null;
    }

    public function update_value($id, string $column, $value): void
    {
        global $wpdb;

        $result = $wpdb->update(
            $this->table->get_name(),
            [$column                 => $value],
            [$this->get_identifier() => $id]
        );

        if ($result === false) {
            throw FailedToEditValue::for_row($id);
        }
    }

    public function insert(array $data): void
    {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table->get_name(),
            $data
        );

        if ($result === false) {
            throw FailedToEditValue::for_row($data[$this->get_identifier()] ?? 0);
        }
    }

    /**
     * @throws FailedToDeleteRowException
     */
    public function delete($id): void
    {
        global $wpdb;

        $results = $wpdb->delete(
            $this->table->get_name(),
            [$this->get_identifier() => $id]
        );

        if ($results === false) {
            throw new FailedToDeleteRowException($id);
        }
    }

}
