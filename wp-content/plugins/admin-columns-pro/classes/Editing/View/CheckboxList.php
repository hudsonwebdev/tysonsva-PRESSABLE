<?php

declare(strict_types=1);

namespace ACP\Editing\View;

use ACP\Editing\View;

class CheckboxList extends View
{
    use OptionsTrait;

    public function __construct(array $options = [])
    {
        parent::__construct('checklist');

        $this->set_options($options);
    }

}
