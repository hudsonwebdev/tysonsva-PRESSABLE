<?php

declare(strict_types=1);

namespace ACA\DataSources\Exception;

use DomainException;

final class InvalidIdentifierException extends DomainException
{
    public function __construct()
    {
        $message = 'Characters allowed: [a-z], [0-9] and underscores and should start with a letter.';

        parent::__construct($message);
    }

}
