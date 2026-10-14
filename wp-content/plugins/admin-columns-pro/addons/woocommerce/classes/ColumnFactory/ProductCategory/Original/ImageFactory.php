<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ProductCategory\Original;

use AC\Meta\QueryMetaFactory;
use AC\MetaType;
use AC\Setting\Config;
use ACP;

class ImageFactory extends ACP\Column\OriginalColumnFactory
{
    private const META_KEY = 'thumbnail_id';

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Image(),
            new ACP\Editing\Storage\Meta(self::META_KEY, MetaType::create_term_meta())
        );
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        $query_meta_factory = new QueryMetaFactory();

        return new ACP\Search\Comparison\Meta\Image(
            self::META_KEY,
            $query_meta_factory->create(self::META_KEY, MetaType::create_term_meta())
        );
    }

}
