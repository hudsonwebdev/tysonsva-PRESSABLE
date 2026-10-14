<?php

declare(strict_types=1);

namespace ACA\DataSources\Exception;

final class TableNotReadableException extends TableException
{
    protected function get_message(string $table): string
    {
        return sprintf('Database table `%s` is not readable.', $table);
    }

}
