<?php

declare(strict_types=1);

namespace ACP\Formatter\Post;

use AC;
use AC\Helper;
use AC\Type\FileSize;
use AC\Type\Value;

class ImagesExtendedLink implements AC\CollectionFormatter
{
    private AC\Value\Extended\ExtendedValue $extended_value;

    public function __construct(AC\Value\Extended\ExtendedValue $extended_value)
    {
        $this->extended_value = $extended_value;
    }

    public function format(AC\Type\ValueCollection $value): Value
    {
        if ($value->count() === 0) {
            throw AC\Exception\ValueNotFoundException::from_id($value->get_id());
        }

        $count = $value->count();
        $id = (int)$value->get_id();

        $total_size = 0;

        $image_helper = Helper\LocalFile::create();
        foreach ($value as $image_url) {
            $total_size += (int)$image_helper->get_size($image_url->get_value());
        }

        $total_size = (string)new FileSize($total_size);

        $label = sprintf(_n('%d image', '%d images', $count, 'codepress-admin-columns'), $count);
        $link = $this->extended_value
            ->get_link($value->get_id(), $label)
            ->with_title(strip_tags(get_the_title($id)) ?: (string)$id)
            ->with_class('-image-container');

        $edit_link = get_edit_post_link($id);

        if ($edit_link) {
            $link = $link->with_edit_link($edit_link);
        }

        return new Value(
            $value->get_id(),
            $link->render() . Helper\Html::create()->rounded($total_size)
        );
    }

}
