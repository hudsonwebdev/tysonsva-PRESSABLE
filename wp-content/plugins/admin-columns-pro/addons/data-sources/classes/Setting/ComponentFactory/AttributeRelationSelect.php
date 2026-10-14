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
use ACP\Sql\Identifier;

class AttributeRelationSelect extends BaseComponentFactory implements InputNameAware
{
    private DataSource $data_source;

    private string $column;

    public function __construct(
        DataSource $data_source,
        string $column
    ) {
        $this->data_source = $data_source;
        $this->column = $column;
    }

    public function get_name(): string
    {
        return Addon::key('attribute_relation');
    }

    protected function get_label(Config $config): ?string
    {
        return __('Attribute', 'codepress-admin-columns');
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

        if ($this->data_source->get_columns()->has($this->column)) {
            $column = Identifier::quote($this->column);

            $query = $this->data_source
                ->get_repository()
                ->query()
                ->select('DISTINCT ' . $column)
                ->order_by_column($column);

            foreach ($query->all() as $row) {
                $value = $row[$this->column];

                $options[$value] = $value;
            }
        }

        return OptionCollection::from_array($options);
    }

}
