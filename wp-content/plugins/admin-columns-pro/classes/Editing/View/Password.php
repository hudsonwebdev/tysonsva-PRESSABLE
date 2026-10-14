<?php

declare(strict_types=1);

namespace ACP\Editing\View;

use ACP\Editing\View;

class Password extends View implements Placeholder, MaxLength
{
    use MaxlengthTrait;
    use PlaceholderTrait;

    public function __construct()
    {
        parent::__construct('password');
    }

}
