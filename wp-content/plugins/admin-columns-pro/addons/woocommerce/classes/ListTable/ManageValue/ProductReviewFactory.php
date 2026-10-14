<?php

declare(strict_types=1);

namespace ACA\WC\ListTable\ManageValue;

use AC\Table\ManageValue\RenderFactory;
use AC\TableScreen;
use AC\TableScreen\ManageValueService;
use AC\TableScreen\ManageValueServiceFactory;
use ACA\WC;

class ProductReviewFactory implements ManageValueServiceFactory
{
    public function can_create(TableScreen $table_screen): bool
    {
        return $table_screen instanceof WC\TableScreen\ProductReview;
    }

    public function create(TableScreen $table_screen, RenderFactory $factory, int $priority = 100): ManageValueService
    {
        return new ProductReview($factory, $priority);
    }

}
