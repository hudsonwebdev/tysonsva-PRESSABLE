<?php

declare(strict_types=1);

namespace ACA\Pods\ColumnFactory\Field\Pick;

use ACA\Pods\Metric\PickOptionsMetricTrait;
use PodsField_Pick;

class PostStatusFactory extends BasePickFactory
{
    use PickOptionsMetricTrait;

    protected function get_options(): array
    {
        return (new PodsField_Pick())->data_post_stati();
    }

}
