<?php

declare(strict_types=1);

namespace ACA\DataSources\Setting\ComponentFactory;

use AC\Setting\AttributeCollection;
use AC\Setting\AttributeFactory;
use AC\Setting\ComponentFactory\BaseComponentFactory;
use AC\Setting\ComponentFactory\InputNameAware;
use AC\Setting\Config;
use AC\Setting\Control\Input;
use AC\Setting\Control\OptionCollection;
use ACA\DataSources\Addon;
use ACA\DataSources\DataSource;

class RelationSelect extends BaseComponentFactory implements InputNameAware
{
    private DataSource $data_source;

    public function __construct(
        DataSource $data_source
    ) {
        $this->data_source = $data_source;
    }

    public function get_name(): string
    {
        return Addon::key('relation');
    }

    protected function get_label(Config $config): ?string
    {
        return __('Column', 'codepress-admin-columns');
    }

    protected function get_input(Config $config): ?Input
    {
        return Input\OptionFactory::create_select(
            $this->get_name(),
            $this->get_options(),
            $config->get($this->get_name()),
            null,
            null,
            new AttributeCollection([
                AttributeFactory::create_refresh(),
            ])
        );
    }

    private function get_options(): OptionCollection
    {
        $options = [];

        foreach ($this->data_source->get_columns() as $column) {
            $options[$column->get_name()] = $column->get_label();
        }

        return OptionCollection::from_array($options);
    }

}
