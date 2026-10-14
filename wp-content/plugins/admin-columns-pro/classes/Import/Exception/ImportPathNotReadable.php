<?php

declare(strict_types=1);

namespace ACP\Import\Exception;

use RuntimeException;

final class ImportPathNotReadable extends RuntimeException
{
    public static function from_path(string $path): self
    {
        return new self(
            sprintf('Import path "%s" does not exist or is not readable.', $path)
        );
    }

}
