<?php

declare(strict_types=1);

namespace ACP\Export;

interface EscapeData
{
    public function escape(string $data): string;

}
