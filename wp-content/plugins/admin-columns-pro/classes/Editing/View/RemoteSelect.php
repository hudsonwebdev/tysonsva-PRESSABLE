<?php

declare(strict_types=1);

namespace ACP\Editing\View;

use ACP\Editing\View;

class RemoteSelect extends View
{
    /**
     * Needs to be paired with RemoteOptions
     * @see \ACP\Editing\RemoteOptions
     */
    public function __construct()
    {
        parent::__construct('select2_remote');
    }

}
