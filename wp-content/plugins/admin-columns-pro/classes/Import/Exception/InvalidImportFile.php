<?php

declare(strict_types=1);

namespace ACP\Import\Exception;

use RuntimeException;
use Throwable;

final class InvalidImportFile extends RuntimeException
{
    public static function from_path(string $path, ?Throwable $previous = null): self
    {
        return new self(
            sprintf('File "%s" does not contain valid import data.', $path),
            0,
            $previous
        );
    }

}
