<?php

declare(strict_types=1);

namespace ACP\Metric\Exception;

use AC\TableScreen;
use InvalidArgumentException;

final class UnsupportedTableScreenException extends InvalidArgumentException
{
    public function __construct(TableScreen $table_screen)
    {
        $message = sprintf('Table screen "%s" is not supported.', get_class($table_screen));

        parent::__construct($message);
    }

}
