<?php

declare(strict_types=1);

namespace ACA\Pods\Metric;

use AC\Setting\Config;
use AC\Type\TableScreenContext;
use ACA\Pods\Field;
use ACP\Metric;

trait PickOptionsMetricTrait
{
    protected TableScreenContext $table_context;

    protected Field $field;

    abstract protected function get_options(): array;

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Choice::from_choices(
            $this->table_context->get_meta_type(),
            $this->field->get_name(),
            $this->get_options()
        );
    }
}
