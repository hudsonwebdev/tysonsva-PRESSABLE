<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Media;

use AC;
use ACP\Helper\Select\OptionsFactory;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType;

class MimeType extends Metric\Config\Post\PostField
{
    public function __construct()
    {
        parent::__construct(
            'post_mime_type',
            new ColumnType\Choice([]) // Load options later
        );
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_choices($this->get_mime_types());
    }

    private function get_mime_types(): array
    {
        $options = (new OptionsFactory\MimeType())->create('attachment');
        $mime_types = [];

        foreach ($options as $option) {
            /** @var AC\Helper\Select\Option $option * */
            $mime_types[$option->get_value()] = $option->get_label();
        }

        return $mime_types;
    }

}
