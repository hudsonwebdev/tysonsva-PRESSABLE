<?php

declare(strict_types=1);

namespace ACP\Editing\View;

use ACP\Editing\View;

trait TagsTrait
{
    public function set_tags(bool $enable_tags): View
    {
        return $this->set('tags', $enable_tags);
    }

}
