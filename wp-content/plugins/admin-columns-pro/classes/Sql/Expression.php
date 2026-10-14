<?php

declare(strict_types=1);

namespace ACP\Sql;

interface Expression
{
    public function __toString(): string;

}
