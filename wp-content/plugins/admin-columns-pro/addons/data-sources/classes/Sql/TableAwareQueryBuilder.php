<?php

declare(strict_types=1);

namespace ACA\DataSources\Sql;

use ACA\DataSources\Repository\Database\Table;
use ACP\Sql\Identifier;
use ACP\Sql\QueryBuilder;

class TableAwareQueryBuilder extends QueryBuilder
{
    protected Table $table;

    public function __construct(Table $table)
    {
        $this->table = $table;

        $this->from($table->get_name());
    }

    public function select_all_columns(): self
    {
        $columns = [];

        foreach ($this->table->get_columns() as $column) {
            $columns[] = Identifier::quote_qualified(
                $this->table->get_name(),
                $column->get_name()
            );
        }

        $this->select_columns($columns);

        return $this;
    }

    private function get_identifier(): string
    {
        return Identifier::quote_qualified(
            $this->table->get_name(),
            $this->table->get_identifier()->get_name()
        );
    }

    public function where_identifier($id): self
    {
        return $this->where(
            $this->get_identifier(),
            $id
        );
    }

    public function order_by_identifier(?string $order = null): self
    {
        return $this->order_by_column(
            $this->get_identifier(),
            $order
        );
    }

}
