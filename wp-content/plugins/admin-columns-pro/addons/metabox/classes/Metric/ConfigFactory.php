<?php

declare(strict_types=1);

namespace ACA\MetaBox\Metric;

use AC\Type\TableScreenContext;
use ACA\MetaBox\Field;
use ACA\MetaBox\Field\DateFormat;
use ACA\MetaBox\MetaboxFieldTypes;
use ACP\Metric;
use ACP\Metric\Config;

final class ConfigFactory
{
    public function create(Field\Field $field, TableScreenContext $table_screen_context): ?Metric\Config
    {
        if ($field->is_table_storage() || $field->is_cloneable()) {
            return null;
        }

        $default_metric = Config\Meta\Text::from_meta_type(
            $table_screen_context->get_meta_type(),
            $field->get_id()
        );

        switch ($field->get_type()) {
            case MetaboxFieldTypes::SELECT:
            case MetaboxFieldTypes::SELECT_ADVANCED:
                $options = $field instanceof Field\Choices ? $field->get_choices() : [];
                $multiple = $field instanceof Field\Multiple && $field->is_multiple();

                return $multiple
                    ? $default_metric
                    : Config\Meta\Choice::from_choices(
                        $table_screen_context->get_meta_type(),
                        $field->get_id(),
                        $options
                    );

            case MetaboxFieldTypes::CHECKBOX:
            case MetaboxFieldTypes::SWITCH:
                return Config\Meta\Choice::from_choices(
                    $table_screen_context->get_meta_type(),
                    $field->get_id(),
                    ['' => 'False', '1' => 'True']
                );
            case MetaboxFieldTypes::RADIO:
                $options = $field instanceof Field\Choices ? $field->get_choices() : [];

                return Config\Meta\Choice::from_choices(
                    $table_screen_context->get_meta_type(),
                    $field->get_id(),
                    $options
                );

            case MetaboxFieldTypes::DATE:
            case MetaboxFieldTypes::DATETIME:
                if (! $field instanceof DateFormat) {
                    return $default_metric;
                }

                $metric = Config\Meta\Date::from_meta_type(
                    $table_screen_context->get_meta_type(),
                    $field->get_id()
                );

                if (! $metric instanceof Config\Meta\Date) {
                    return $default_metric;
                }

                switch ($field->get_date_format()) {
                    case 'U':
                        return $metric->as_timestamp();
                    case 'Y-m-d':
                    case 'Y-m-d H:i:s':
                        return $metric;
                    default:
                        return $default_metric;
                }

                // no break
            case MetaboxFieldTypes::NUMBER:
            case MetaboxFieldTypes::SLIDER:
            case MetaboxFieldTypes::RANGE:
                return Config\Meta\Numeric::from_meta_type(
                    $table_screen_context->get_meta_type(),
                    $field->get_id()
                );
            default:
                return Config\Meta\Text::from_meta_type(
                    $table_screen_context->get_meta_type(),
                    $field->get_id()
                );
        }
    }
}
