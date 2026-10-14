<?php

declare(strict_types=1);

namespace ACP\Editing\View;

use ACP\Editing\View;

class AdvancedSelect extends View
{
    use MethodTrait;
    use MultipleTrait;
    use OptionsTrait;

    public function __construct(array $options = [])
    {
        parent::__construct('select2_dropdown');

        $this->set_options($options);
    }

}
