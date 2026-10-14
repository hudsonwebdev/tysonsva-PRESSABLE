<?php

declare(strict_types=1);

namespace ACA\DataSources;

use ACA\DataSources\Sql\TableAwareQueryBuilder;
use ACP\Exception\FailedToEditValue;

interface Repository
{
    public function query(): TableAwareQueryBuilder;

    public function get_table(): string;

    public function get_identifier(): string;

    public function find($id): ?array;

    /**
     * Returns the value of a single column
     */
    public function get_value($id, string $column);

    /**
     * Update the value of a single column
     *
     * @throws FailedToEditValue
     */
    public function update_value($id, string $column, $value): void;

    /**
     * Insert a new row
     *
     * @param array<string, mixed> $data
     *
     * @throws FailedToEditValue
     */
    public function insert(array $data): void;

    public function delete($id): void;

}
