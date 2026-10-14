<?php

declare(strict_types=1);

namespace ACA\ACF\Metric;

use AC\Type\TableScreenContext;
use ACA\ACF\Field;
use ACA\ACF\FieldType;
use ACP\Metric;
use ACP\Metric\Config\Meta\Choice;

class ConfigFactory
{
    public function create(Field $field, TableScreenContext $table_context): ?Metric\Config
    {
        switch ($field->get_type()) {
            case FieldType::TYPE_NUMBER:
            case FieldType::TYPE_RANGE:
                return Metric\Config\Meta\Numeric::from_meta_type(
                    $table_context->get_meta_type(),
                    $field->get_meta_key()
                );
            case FieldType::TYPE_DATE_PICKER:
                return AcfDate::from_meta_type(
                    $table_context->get_meta_type(),
                    $field->get_meta_key()
                );
            case FieldType::TYPE_DATE_TIME_PICKER:
                return Metric\Config\Meta\Date::from_meta_type(
                    $table_context->get_meta_type(),
                    $field->get_meta_key()
                );
            case FieldType::TYPE_SELECT:
                if ($field instanceof Field\Multiple && $field->is_multiple()) {
                    return null;
                }

                return $this->create_choices($field, $table_context);
            case FieldType::TYPE_RADIO:
            case FieldType::TYPE_BUTTON_GROUP:
                return $this->create_choices($field, $table_context);
            case FieldType::TYPE_CHECKBOX:
            case FieldType::TYPE_REPEATER:
            case FieldType::TYPE_FLEXIBLE_CONTENT:
            case FieldType::TYPE_GROUP:
            case FieldType::TYPE_CLONE:
            case FieldType::TYPE_TAB:
            case FieldType::TYPE_MESSAGE:
                return null;
            case FieldType::TYPE_BOOLEAN:
                return Metric\Config\Meta\Choice::from_choices(
                    $table_context->get_meta_type(),
                    $field->get_meta_key(),
                    [
                        '0' => _x('Off', 'ACF Boolean', 'codepress-admin-columns'),
                        '1' => _x('On', 'ACF Boolean', 'codepress-admin-columns'),
                    ]
                );
            default:
                return Metric\Config\Meta\Text::from_meta_type(
                    $table_context->get_meta_type(),
                    $field->get_meta_key()
                );
        }
    }

    private function create_choices(Field $field, TableScreenContext $table_context): ?Metric\Config
    {
        if (! $field instanceof Field\Choices) {
            return null;
        }

        $choices = $field->get_choices();

        if (! $choices) {
            return null;
        }

        return Choice::from_choices(
            $table_context->get_meta_type(),
            $field->get_meta_key(),
            $choices
        );
    }

}
