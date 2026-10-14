<?php

declare(strict_types=1);

namespace ACP\Sorting\FormatValue;

use AC\Helper;
use ACP\Sorting\FormatValue;

class ContentTotalImageSize implements FormatValue
{
    public function format_value($value)
    {
        $urls = array_unique(Helper\Strings::create()->get_image_urls_from_string((string)$value));

        $total_size = 0;

        $image_helper = Helper\LocalFile::create();
        foreach ($urls as $url) {
            $size = $image_helper->get_size($url);

            if ($size > 0) {
                $total_size += $size;
            }
        }

        return $total_size > 0
            ? $total_size
            : false;
    }

}
