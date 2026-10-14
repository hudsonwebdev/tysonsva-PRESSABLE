<?php

use AC\Setting\Control\Type\Option;
use ACA\DataSources\DataSource\ColumnType;

return [
    [
        ColumnType\BooleanType::class,
        new Option(
            __('Boolean', 'codepress-admin-columns'),
            'boolean'
        ),
    ],
    [
        ColumnType\ColorType::class,
        new Option(
            __('Color', 'codepress-admin-columns'),
            'color'
        ),
    ],
    [
        ColumnType\DateTimeType::class,
        new Option(
            __('Date', 'codepress-admin-columns'),
            'date-time'
        ),
    ],
    [
        ColumnType\NumberType::class,
        new Option(
            __('Number', 'codepress-admin-columns'),
            'number'
        ),
    ],
    [
        ColumnType\SelectColumnType::class,
        new Option(
            __('Select', 'codepress-admin-columns'),
            'select'
        ),
    ],
    [
        ColumnType\SerializedType::class,
        new Option(
            __('Structured Data', 'codepress-admin-columns') . ' (Array, JSON, Serialized)',
            'serialized'
        ),
    ],
    [
        ColumnType\TextType::class,
        new Option(
            __('Text', 'codepress-admin-columns'),
            'text'
        ),
    ],
    [
        ColumnType\UrlType::class,
        new Option(
            __('URL', 'codepress-admin-columns'),
            'url'
        ),
    ],
    [
        ColumnType\EmailType::class,
        new Option(
            __('Email', 'codepress-admin-columns'),
            'email'
        ),
    ],
    [
        ColumnType\ImageType::class,
        new Option(
            __('Image', 'codepress-admin-columns'),
            'image'
        ),
    ],
    [
        ColumnType\WordPressPostType::class,
        new Option(
            __('Related Post', 'codepress-admin-columns'),
            'wordpress_post'
        ),
    ],
    [
        ColumnType\WordPressUserType::class,
        new Option(
            __('Related User', 'codepress-admin-columns'),
            'wordpress_user'
        ),
    ],
    [
        ColumnType\WordPressMediaType::class,
        new Option(
            __('Related Media', 'codepress-admin-columns'),
            'wordpress_media'
        ),
    ],
];