<?php

declare(strict_types=1);

namespace ACP\Import;

use AC\Type\ListScreenStatus;

final class ImportOptions
{
    private ?ListScreenStatus $status;

    private function __construct(?ListScreenStatus $status)
    {
        $this->status = $status;
    }

    public static function create(): self
    {
        return new self(null);
    }

    public function with_status(ListScreenStatus $status): self
    {
        return new self($status);
    }

    /**
     * Maps to the $overwrites array consumed by ACP\Tools\ImportHandler.
     * An unset status yields no overwrite, so the file's stored status is kept.
     *
     * @return array<string, ListScreenStatus>
     */
    public function to_overwrites(): array
    {
        if ($this->status === null) {
            return [];
        }

        return ['status' => $this->status];
    }

}
