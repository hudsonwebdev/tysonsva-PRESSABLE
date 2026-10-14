<?php

declare(strict_types=1);

namespace ACP\Editing\View;

use ACP\Editing\View;

trait StepTrait
{
    public function set_step(string $step): View
    {
        return $this->set('range_step', $step);
    }

}
