<?php

declare(strict_types=1);

namespace ACP\Editing\View;

use ACP\Editing\View;

class Date extends View
{
    use WeekstartTrait;

    public function __construct()
    {
        parent::__construct('date');

        $this->set_week_start(1);
    }

}
