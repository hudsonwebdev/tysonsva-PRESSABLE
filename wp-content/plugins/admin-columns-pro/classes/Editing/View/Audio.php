<?php

declare(strict_types=1);

namespace ACP\Editing\View;

use ACP\Editing\View;

class Audio extends View
{
    use AttachmentTypeTrait;
    use MultipleTrait;

    public function __construct()
    {
        parent::__construct('media');

        $this->set_attachment_type('audio');
    }

}
