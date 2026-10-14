<?php

declare(strict_types=1);

namespace ACA\MetaBox\ColumnFactory\Meta;

use AC\CollectionFormatter;
use AC\Formatter;
use AC\Formatter\Collection\Implode;
use AC\Formatter\Collection\Separator;
use AC\Formatter\EmptyValue;
use AC\Formatter\HtmlEntityDecode;
use AC\Formatter\StringSanitizer;
use AC\FormatterCollection;
use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory;
use AC\Setting\Config;
use ACA\MetaBox;
use ACA\MetaBox\Value;
use ACA\MetaBox\Value\Formatter\FileNames;
use ACP\Formatter\SingleValue;

final class GroupCloneFactory extends GroupFactory
{
    private const EXPORT_SEPARATOR = ' | ';

    protected function get_raw_formatter(Config $config): FormatterCollection
    {
        return new FormatterCollection([
            new Value\Formatter\GroupCloneField(
                $this->table_context->get_meta_type(),
                $this->field->get_id(),
                $config->get('group_field', '')
            ),
        ]);
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        $settings = parent::get_settings($config);
        $settings->add((new ComponentFactory\Separator())->create($config));

        return $settings;
    }

    /**
     * A clone row without a usable value has to keep its position, so it stays aligned with the other rows
     */
    protected function prepare_sub_field_formatter($formatter)
    {
        return $formatter instanceof Formatter && ! $formatter instanceof CollectionFormatter
            ? new SingleValue($formatter)
            : $formatter;
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        // Without a number of items key the clone rows are never truncated
        return parent::get_formatters($config)
                     ->with_formatter(new EmptyValue())
                     ->with_formatter(Separator::create_from_settings($config, 'separator', ''));
    }

    protected function get_export(Config $config): ?FormatterCollection
    {
        $sub_field = $this->get_sub_field($config);

        if (! $sub_field) {
            return null;
        }

        switch ($sub_field->get_type()) {
            case MetaBox\MetaboxFieldTypes::SWITCH:
            case MetaBox\MetaboxFieldTypes::CHECKBOX:
                $formatters = $this->get_raw_formatter($config)
                                   ->with_formatter(new EmptyValue('0'));

                break;
            case MetaBox\MetaboxFieldTypes::IMAGE_ADVANCED:
            case MetaBox\MetaboxFieldTypes::IMAGE:
            case MetaBox\MetaboxFieldTypes::SINGLE_IMAGE:
                $formatters = $this->get_raw_formatter($config)
                                   ->with_formatter(new SingleValue(new FileNames()))
                                   ->with_formatter(new EmptyValue());

                break;
            default:
                $formatters = parent::get_formatters($config)
                                    ->with_formatter(new SingleValue(new StringSanitizer()))
                                    ->with_formatter(new EmptyValue());
        }

        // Clone rows are separated by a pipe, values within a single row by a comma
        return $formatters
            ->with_formatter(new HtmlEntityDecode())
            ->with_formatter(new Implode(self::EXPORT_SEPARATOR));
    }

}
