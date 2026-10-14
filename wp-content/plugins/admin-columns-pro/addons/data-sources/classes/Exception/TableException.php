<?php

declare(strict_types=1);

namespace ACA\DataSources\Exception;

use RuntimeException;

abstract class TableException extends RuntimeException
{
    protected string $table;

    public function __construct(string $table)
    {
        $this->table = $table;

        parent::__construct($this->get_message($table));
    }

    abstract protected function get_message(string $table): string;

    public function get_table(): string
    {
        return $this->table;
    }

}
