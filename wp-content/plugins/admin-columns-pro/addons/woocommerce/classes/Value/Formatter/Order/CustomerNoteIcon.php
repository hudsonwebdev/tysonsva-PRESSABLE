<?php

declare(strict_types=1);

namespace ACA\WC\Value\Formatter\Order;

use AC\Formatter;
use AC\Helper;
use AC\Helper\Dashicon;
use AC\Type\Value;

class CustomerNoteIcon implements Formatter
{
    public function format(Value $value)
    {
        $icon = Dashicon::create('media-text', 'gray')->render();

        return $value->with_value(Helper\Html::create()->tooltip($icon, $value->get_value()));
    }

}
