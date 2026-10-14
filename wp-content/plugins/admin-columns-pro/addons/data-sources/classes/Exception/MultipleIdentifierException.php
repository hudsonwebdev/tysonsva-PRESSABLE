<?php

declare(strict_types=1);

namespace ACA\DataSources\Exception;

final class MultipleIdentifierException extends TableException
{
    protected function get_message(string $table): string
    {
        return sprintf('Database schema `%s` has multiple identifiers, which is not supported.', $table);
    }

}
