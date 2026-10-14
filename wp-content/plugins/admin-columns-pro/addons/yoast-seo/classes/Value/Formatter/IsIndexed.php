<?php

declare(strict_types=1);

namespace ACA\YoastSeo\Value\Formatter;

use AC;
use AC\Helper\Dashicon;
use AC\Type\Value;
use WPSEO_Post_Type;

class IsIndexed implements AC\Formatter
{
    private string $post_type;

    public function __construct(string $post_type)
    {
        $this->post_type = $post_type;
    }

    public function format(Value $value): Value
    {
        $meta_value = (int)get_post_meta($value->get_id(), '_yoast_wpseo_meta-robots-noindex', true);

        switch ($meta_value) {
            case 0:
                return $value->with_value(
                    sprintf(
                        '%s <span style="color: #ccc;">%s</span>',
                        Dashicon::yes_or_no($this->get_default_post_type_index())->render(),
                        Dashicon::create('info', 'grey')
                                ->with_tooltip(__('Implicit', 'codepress-admin-columns'))
                                ->render()
                    )
                );
            case 1:
                return $value->with_value(Dashicon::no()->render());
            case 2:
                return $value->with_value(Dashicon::yes()->render());
            default:
                return $value->with_value(false);
        }
    }

    private function get_default_post_type_index(): bool
    {
        if (! class_exists('WPSEO_Post_Type', false)) {
            return false;
        }

        return WPSEO_Post_Type::is_post_type_indexable($this->post_type);
    }
}
