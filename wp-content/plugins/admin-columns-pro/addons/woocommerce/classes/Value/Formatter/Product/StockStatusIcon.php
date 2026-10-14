<?php

declare(strict_types=1);

namespace ACA\WC\Value\Formatter\Product;

use AC\Formatter;
use AC\Helper\Dashicon;
use AC\Type\Value;

class StockStatusIcon implements Formatter
{
    public function format(Value $value)
    {
        switch ($value->get_value()) {
            case 'instock' :
                return $value->with_value(Dashicon::yes(__('In stock', 'codepress-admin-columns'))->render());
            case 'outofstock' :
                return $value->with_value(Dashicon::no(__('Out of stock', 'codepress-admin-columns'))->render());
            case 'onbackorder' :
                return $value->with_value(
                    Dashicon::create('backup', 'yellow')
                             ->with_tooltip(__('On backorder', 'codepress-admin-columns'))
                             ->render()
                );
            default:
                return $value;
        }
    }

}
