<?php

declare(strict_types=1);

namespace ACA\DataSources\Exception;

final class MissingIdentifierException extends TableException
{
    protected function get_message(string $table): string
    {
        return sprintf('Database schema `%s` is missing an identifier.', $table);
    }

}
