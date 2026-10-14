<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Post;

use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType;
use ACP\Metric\Type\AggregationType;

class Id extends PostField
{
    public function __construct()
    {
        parent::__construct(
            'ID',
            new ColumnType\Numeric()
        );
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::create_from_array([
            AggregationType::RANGE,
            AggregationType::MIN,
            AggregationType::MAX,
        ]);
    }

}
