<?php

declare(strict_types=1);

namespace ACA\DataSources\Export\Strategy;

use AC\Type\Value;
use AC\Type\ValueCollection;
use ACA\DataSources;
use ACP\Export\Exporter\TableDataFactory;
use ACP\Export\ResponseFactory;
use ACP\Export\Strategy;
use ACP\Sql\QueryBuilder;

class DataSource extends Strategy
{
    private ResponseFactory $response_factory;

    private TableDataFactory $data_factory;

    private DataSources\TableScreen $table_screen;

    public function __construct(
        ResponseFactory $response_factory,
        TableDataFactory $data_factory,
        DataSources\TableScreen $table_screen
    ) {
        $this->response_factory = $response_factory;
        $this->data_factory = $data_factory;
        $this->table_screen = $table_screen;
    }

    public function handle_export(): void
    {
        add_filter('acp/data-sources/pre_get_items', [$this, 'catch_export']);
    }

    public function catch_export(QueryBuilder $query): void
    {
        $data_source = $this->table_screen->get_data_source();
        $identifier = $data_source->get_repository()->get_identifier();

        if ($this->ids) {
            $query->and_where_in($identifier, $this->ids);
        }

        $query
            ->select()
            ->limit(
                $this->items_per_iteration,
                $this->counter * $this->get_items_per_iteration()
            );

        $values = new ValueCollection(0);

        foreach ($query->all() as $row) {
            $values->add(
                new Value(
                    $row[$identifier],
                    $row
                )
            );
        }

        if (null === $this->columns) {
            return;
        }

        $data = $this->data_factory->create(
            $this->columns,
            $values,
            0 === $this->counter
        );

        $this->response_factory->create($data);
        exit;
    }

}
