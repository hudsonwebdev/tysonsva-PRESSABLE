<?php

declare(strict_types=1);

namespace ACA\DataSources\Editing\Storage;

use ACA\DataSources\DataSource;
use ACA\DataSources\Sql\TableAwareQueryBuilder;
use ACP\Editing;
use ACP\Exception\FailedToEditValue;

class Relation implements Editing\Storage
{
    protected DataSource $data_source;

    protected DataSource\Relation $relation;

    protected string $column;

    public function __construct(
        DataSource $data_source,
        DataSource\Relation $relation,
        string $column
    ) {
        $this->data_source = $data_source;
        $this->relation = $relation;
        $this->column = $column;
    }

    protected function get_builder($id): TableAwareQueryBuilder
    {
        $foreign_repository = $this->relation->get_foreign_data_source()->get_repository();

        return $foreign_repository
            ->query()
            ->select()
            ->where(
                $this->relation->get_foreign_column(),
                $id
            );
    }

    protected function find_one($id): array
    {
        return $this->get_builder($this->get_id($id))->one();
    }

    public function get(int $id)
    {
        $row = $this->find_one($id);

        return $row[$this->column] ?? null;
    }

    protected function get_id($id)
    {
        $repository = $this->data_source->get_repository();
        $local_column = $this->relation->get_local_column();

        if ($local_column !== $repository->get_identifier()) {
            $id = $repository->get_value($id, $local_column);
        }

        return $id;
    }

    public function update(int $id, $data): bool
    {
        $row = $this->find_one($id);

        if (! $row) {
            throw FailedToEditValue::for_row($id);
        }

        $data_source = $this->relation->get_foreign_data_source();
        $repository = $data_source->get_repository();

        $repository->update_value(
            $row[$repository->get_identifier()],
            $this->column,
            $data
        );

        return true;
    }

}
