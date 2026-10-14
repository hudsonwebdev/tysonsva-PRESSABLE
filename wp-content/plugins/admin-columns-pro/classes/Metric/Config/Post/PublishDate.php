<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Post;

use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType;

class PublishDate extends PostField
{
    public function __construct()
    {
        parent::__construct(
            'post_date',
            new ColumnType\Date()
        );
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_date();
    }

}
