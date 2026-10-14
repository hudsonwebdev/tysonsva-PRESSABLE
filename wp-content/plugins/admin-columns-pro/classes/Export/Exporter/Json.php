<?php

declare(strict_types=1);

namespace ACP\Export\Exporter;

final class Json
{
    private array $rows;

    private array $headers;

    public function __construct(array $rows, array $headers)
    {
        $this->rows = $rows;
        $this->headers = $headers;
    }

    public function get_contents(): array
    {
        return array_values($this->rows);
    }

    public function get_headers(): array
    {
        return $this->headers;
    }

    public function count_rows(): int
    {
        return count($this->rows);
    }

}
