<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\Comparison;

use AC\Helper\Select\Options;
use AC\Type\ToggleOptions;
use ACA\DataSources;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class ToggleComparison extends DataSources\Search\Comparison implements Values
{
    private ToggleOptions $options;

    public function __construct(string $id, ToggleOptions $options)
    {
        parent::__construct(
            $id,
            new Operators([
                Operators::EQ,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ])
        );

        $this->options = $options;
    }

    public function get_values(): Options
    {
        return Options::create_from_array([
            $this->options->get_disabled()->get_value() => __('False', 'codepress-admin-columns'),
            $this->options->get_enabled()->get_value()  => __('True', 'codepress-admin-columns'),
        ]);
    }

}
