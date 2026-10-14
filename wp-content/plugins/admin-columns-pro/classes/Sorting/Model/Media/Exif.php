<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;

class Exif extends AttachmentMetaData
{
    public function __construct(string $field)
    {
        parent::__construct(new FormatValue\Exif($field));
    }

}
