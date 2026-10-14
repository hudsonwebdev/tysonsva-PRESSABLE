<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactories;

use AC;
use AC\ColumnFactoryDefinitionCollection;
use AC\TableScreen;
use ACA\WC\TableScreen\ProductReview;
use ACP;

class ProductReviewFactory extends AC\ColumnFactories\BaseFactory
{
    protected function get_factories(TableScreen $table_screen): ColumnFactoryDefinitionCollection
    {
        $collection = new ColumnFactoryDefinitionCollection();

        if (! $table_screen instanceof ProductReview) {
            return $collection;
        }

        $factories = [
            ACP\ColumnFactory\CustomFieldFactory::class,
        ];

        foreach ($factories as $factory) {
            $collection->add(new AC\Type\ColumnFactoryDefinition($factory));
        }

        return $collection;
    }
}
