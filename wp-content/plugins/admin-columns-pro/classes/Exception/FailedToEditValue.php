<?php

declare(strict_types=1);

namespace ACP\Exception;

use RuntimeException;

final class FailedToEditValue extends RuntimeException
{
    /**
     * @var string|int
     */
    private $id;

    public function __construct(string $message, $id)
    {
        $this->id = $id;

        parent::__construct($message);
    }

    public static function for_row($id): self
    {
        $message = sprintf('Failed to update value for row with id %s.', $id);

        return new self($message, $id);
    }

    /**
     * @return int|string
     */
    public function get_id()
    {
        return $this->id;
    }

}
