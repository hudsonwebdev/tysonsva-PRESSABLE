<?php

declare(strict_types=1);

namespace ACP\Editing\RequestHandler\Exception;

use RuntimeException;

class NotEditableException extends RuntimeException
{
    public function __construct($message = null)
    {
        parent::__construct($message ?: __('Item can not be edited.', 'codepress-admin-columns'));
    }

}
