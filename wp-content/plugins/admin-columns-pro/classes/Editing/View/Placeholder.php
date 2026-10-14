<?php

declare(strict_types=1);

namespace ACP\Editing\View;

use ACP\Editing\View;

interface Placeholder
{
    public function set_placeholder(string $placeholder): View;

}
