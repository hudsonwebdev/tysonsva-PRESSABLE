<?php

declare(strict_types=1);

namespace ACP\Metric\Service;

use AC;
use AC\Registerable;
use ACP\Metric\Strategy\AggregateFactory;
use ACP\Metric\TableElement\Metric;
use ACP\Settings\ListScreen\TableElements;

class Admin implements Registerable
{
    private AggregateFactory $aggregate_factory;

    public function __construct(AggregateFactory $aggregate_factory)
    {
        $this->aggregate_factory = $aggregate_factory;
    }

    public function register(): void
    {
        add_action('ac/admin/settings/table_elements', [$this, 'add_table_elements'], 10, 2);
    }

    public function add_table_elements(TableElements $collection, AC\TableScreen $table_screen): void
    {
        if ($this->aggregate_factory->supports($table_screen)) {
            $collection->add(new Metric(), 45);
        }
    }

}
