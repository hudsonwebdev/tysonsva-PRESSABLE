<?php

use ACA\DataSources\ColumnFactory;
use ACA\DataSources\DataSource\ColumnType;

return [
    ColumnType\IdentifierType::class     => ColumnFactory\IdentifierFactory::class,
    ColumnType\ConfigurableType::class   => ColumnFactory\ConfigurableFactory::class,
    ColumnType\BooleanType::class        => ColumnFactory\BooleanFactory::class,
    ColumnType\ColorType::class          => ColumnFactory\ColorFactory::class,
    ColumnType\DateTimeType::class       => ColumnFactory\DateTimeFactory::class,
    ColumnType\NumberType::class         => ColumnFactory\NumberFactory::class,
    ColumnType\SelectColumnType::class   => ColumnFactory\SelectFactory::class,
    ColumnType\SerializedType::class     => ColumnFactory\SerializedFactory::class,
    ColumnType\TextType::class           => ColumnFactory\TextFactory::class,
    ColumnType\UrlType::class            => ColumnFactory\UrlFactory::class,
    ColumnType\EmailType::class          => ColumnFactory\EmailFactory::class,
    ColumnType\ImageType::class          => ColumnFactory\ImageFactory::class,
    ColumnType\WordPressPostType::class  => ColumnFactory\WordPressPostFactory::class,
    ColumnType\WordPressUserType::class  => ColumnFactory\WordPressUserFactory::class,
    ColumnType\WordPressMediaType::class => ColumnFactory\WordPressMediaFactory::class,
];
