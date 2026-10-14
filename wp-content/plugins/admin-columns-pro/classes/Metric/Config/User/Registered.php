<?php

declare(strict_types=1);

namespace ACP\Metric\Config\User;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType\Date;
use ACP\Metric\Type\AggregationType;

class Registered extends Metric\Config\Base
{
    private Date $column_type;

    public function __construct()
    {
        $this->column_type = new Date();
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return $this->column_type->get_aggregation_types();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return $this->column_type->create_formatter($aggregation_type);
    }

    protected function get_column(string $alias): string
    {
        global $wpdb;

        return $this->column_type->get_column_expression($wpdb->users . '.user_registered');
    }

}
