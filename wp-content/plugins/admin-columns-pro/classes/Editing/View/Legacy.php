<?php

declare(strict_types=1);

namespace ACP\Editing\View;

use ACP\Editing\View;

class Legacy extends View
{
    public function __construct(array $args)
    {
        parent::__construct($args['type']);

        $this->args = $args;
    }

}
