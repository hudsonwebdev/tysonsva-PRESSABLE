<?php

declare(strict_types=1);

namespace ACP\Sorting\FormatValue;

use AC\Helper;
use ACP\Sorting\FormatValue;

class WordCount implements FormatValue
{
    public function format_value($value)
    {
        return Helper\Strings::create()->word_count((string)$value);
    }

}
