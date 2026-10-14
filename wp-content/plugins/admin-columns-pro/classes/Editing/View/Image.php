<?php

declare(strict_types=1);

namespace ACP\Editing\View;

use ACP\Editing\View;

class Image extends View
{
    use AttachmentTypeTrait;
    use MediaUploadToTrait;
    use MultipleTrait;

    public function __construct()
    {
        parent::__construct('media');

        $this->set_attachment_type('image');
    }

}
