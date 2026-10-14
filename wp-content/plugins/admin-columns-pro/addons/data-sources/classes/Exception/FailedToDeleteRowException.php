<?php

declare(strict_types=1);

namespace ACA\DataSources\Exception;

use RuntimeException;

class FailedToDeleteRowException extends RuntimeException
{
    public function __construct($id)
    {
        $message = sprintf('Failed to delete row for id %s.', $id);

        parent::__construct($message);
    }

}
