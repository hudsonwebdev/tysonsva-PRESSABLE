<?php

declare(strict_types=1);

namespace ACA\WC\Value\Formatter\Product;

use AC\Exception\ValueNotFoundException;
use AC\Formatter;
use AC\Helper;
use AC\Helper\Dashicon;
use AC\Type\Value;

class BackordersFormatted implements Formatter
{
    public function format(Value $value)
    {
        switch ($value->get_value()) {
            case 'no':
                return $value->with_value(
                    Dashicon::no(__('Do not allow backorders', 'codepress-admin-columns'))->render()
                );
            case 'yes':
                return $value->with_value(Dashicon::yes(__('Allow backorders', 'codepress-admin-columns'))->render());
            case 'notify':
                $icon_email = Dashicon::create('email-alt')->render();

                return $value->with_value(
                    Helper\Html::create()->tooltip(
                        Dashicon::yes()->render() . $icon_email,
                        __('Yes, but notify customer', 'woocommerce')
                    )
                );
            default:
                throw ValueNotFoundException::from_id($value->get_id());
        }
    }

}
