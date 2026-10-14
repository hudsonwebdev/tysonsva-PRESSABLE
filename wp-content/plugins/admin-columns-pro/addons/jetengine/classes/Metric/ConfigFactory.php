<?php

declare(strict_types=1);

namespace ACA\JetEngine\Metric;

use AC\Type\TableScreenContext;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\Type;
use ACP\Metric;

final class ConfigFactory
{
    public function create(Field $field, TableScreenContext $table_context): ?Metric\Config
    {
        switch (true) {
            case $field instanceof Type\Number:
                return Metric\Config\Meta\Numeric::from_meta_type(
                    $table_context->get_meta_type(),
                    $field->get_name()
                );
            case $field instanceof Type\Switcher:
                return Metric\Config\Meta\Choice::from_choices(
                    $table_context->get_meta_type(),
                    $field->get_name(),
                    [
                        'false' => _x('Off', 'JetEngine Switch', 'codepress-admin-columns'),
                        'true'  => _x('On', 'JetEngine Switch', 'codepress-admin-columns'),
                    ]
                );
            case $field instanceof Type\Radio:
                return Metric\Config\Meta\Choice::from_choices(
                    $table_context->get_meta_type(),
                    $field->get_name(),
                    $field->get_options()
                );
            case $field instanceof Type\Select:

                if ($field->is_multiple()) {
                    return Metric\Config\Meta\Text::from_meta_type(
                        $table_context->get_meta_type(),
                        $field->get_name()
                    );
                }

                return Metric\Config\Meta\Choice::from_choices(
                    $table_context->get_meta_type(),
                    $field->get_name(),
                    $field->get_options()
                );
            case $field instanceof Type\Date:
                $metric = Metric\Config\Meta\Date::from_meta_type(
                    $table_context->get_meta_type(),
                    $field->get_name()
                );

                if (! $metric instanceof Metric\Config\Meta\Date) {
                    return null;
                }

                return $field->is_timestamp()
                    ? $metric->as_timestamp()
                    : $metric;

            default:
                return Metric\Config\Meta\Text::from_meta_type(
                    $table_context->get_meta_type(),
                    $field->get_name()
                );
        }
    }

}
