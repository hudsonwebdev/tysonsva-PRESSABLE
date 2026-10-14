<?php

declare(strict_types=1);

namespace ACP\Editing;

use RuntimeException;

interface Storage
{
    /**
     * @return mixed
     */
    public function get(int $id);

    /**
     * @param mixed $data
     *
     * @throws RuntimeException
     */
    public function update(int $id, $data): bool;

}
