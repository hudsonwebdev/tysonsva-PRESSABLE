<?php

declare(strict_types=1);

namespace ACA\DataSources\ListTable\ManageValue;

use AC;
use AC\Exception\ValueNotFoundException;
use AC\Type\Value;

class DataSource implements AC\TableScreen\ManageValueService
{
    private AC\Table\ManageValue\RenderFactory $factory;

    public function __construct(AC\Table\ManageValue\RenderFactory $factory)
    {
        $this->factory = $factory;
    }

    public function register(): void
    {
        $action = 'manage_data_source_column_value';

        if (did_action($action)) {
            throw AC\Exception\HookTimingException::called_too_early($action);
        }

        add_filter($action, [$this, 'render_value'], 10, 3);
    }

    /**
     * @throws ValueNotFoundException
     */
    public function render_value(...$args): Value
    {
        [$value, $column_id] = $args;

        $formatter = $this->factory->create(new AC\Type\ColumnId((string)$column_id));

        if ($formatter) {
            return $formatter->format($value);
        }

        return $value;
    }

}
