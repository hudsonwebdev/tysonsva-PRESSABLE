<?php

declare(strict_types=1);

namespace ACA\WC\Editing\Storage\Product\Attributes;

use ACA\WC\Editing\Storage;
use ACA\WC\Helper\Attributes;
use WC_Product_Attribute;

class Custom extends Storage\Product\Attributes
{
    protected function create_attribute(): ?WC_Product_Attribute
    {
        $labels = (new Attributes())->get_custom_labels();

        if (! isset($labels[$this->attribute])) {
            return null;
        }

        $attribute = new WC_Product_Attribute();
        $attribute->set_name($labels[$this->attribute]);

        return $attribute;
    }

}
