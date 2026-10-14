<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\Comparison;

use AC\Helper\Select\Options;
use ACA\DataSources;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class OptionComparison extends DataSources\Search\Comparison implements Values
{
    private array $options;

    public function __construct(string $id, array $options)
    {
        parent::__construct(
            $id,
            new Operators([
                Operators::EQ,
                Operators::NEQ,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ])
        );

        $this->options = $options;
    }

    public function get_values(): Options
    {
        return Options::create_from_array($this->options);
    }

}
