<?php

declare(strict_types=1);

namespace ACA\WC\Value\Formatter\Product;

use AC\CollectionFormatter;
use AC\Type\Value;
use AC\Type\ValueCollection;
use ACA\WC\Value\ExtendedValue;

class GroupedProductsExtended implements CollectionFormatter
{
    public function format(ValueCollection $collection): Value
    {
        $product = wc_get_product($collection->get_id());

        $link = new ExtendedValue\Product\GroupedProducts();
        $link = $link->get_link($collection->get_id(), (string)$collection->count())
                     ->with_title(
                         sprintf(
                             __('Grouped products for %s', 'codepress-admin-columns'),
                             sprintf('”%s”', $product->get_title())
                         )
                     );

        $edit = get_edit_post_link($product->get_id());

        if ($edit) {
            $link->with_edit_link($edit);
        }

        return new Value($collection->get_id(), $link->render());
    }

}
