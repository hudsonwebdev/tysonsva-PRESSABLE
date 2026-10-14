<?php

declare(strict_types=1);

namespace ACA\DataSources\Setting\ComponentFactory;

use AC\Setting\AttributeCollection;
use AC\Setting\AttributeFactory;
use AC\Setting\ComponentFactory;
use AC\Setting\ComponentFactory\BaseComponentFactory;
use AC\Setting\Config;
use AC\Setting\Control\Input;
use ACA\DataSources;
use ACA\DataSources\Addon;

class ColumnTypeSelect extends BaseComponentFactory implements ComponentFactory\InputNameAware
{
    private DataSources\Setting\ColumnTypeOptionMap $map;

    private ?string $default_column_type;

    public function __construct(
        DataSources\Setting\ColumnTypeOptionMap $map,
        ?string $default_column_type = null
    ) {
        $this->map = $map;
        $this->default_column_type = $default_column_type;
    }

    public function get_name(): string
    {
        return Addon::key('field_type');
    }

    protected function get_label(Config $config): ?string
    {
        return __('Field Type', 'codepress-admin-columns');
    }

    protected function get_input(Config $config): ?Input
    {
        $default_value = null;

        if (is_string($this->default_column_type)) {
            $option = $this->map->get_option($this->default_column_type);

            if ($option) {
                $default_value = $option->get_value();
            }
        }

        return Input\OptionFactory::create_select(
            $this->get_name(),
            $this->map->get_options(),
            $config->get($this->get_name(), $default_value),
            null,
            null,
            new AttributeCollection([
                AttributeFactory::create_refresh(),
            ])
        );
    }

}
