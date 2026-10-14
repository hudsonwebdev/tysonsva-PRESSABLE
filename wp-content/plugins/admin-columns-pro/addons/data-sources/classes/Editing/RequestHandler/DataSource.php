<?php

declare(strict_types=1);

namespace ACA\DataSources\Editing\RequestHandler;

use AC\Request;
use ACA\DataSources;
use ACP\Editing\ApplyFilter\RowsPerIteration;
use ACP\Editing\RequestHandler;
use ACP\Editing\Response;
use ACP\Sql\QueryBuilder;

class DataSource implements RequestHandler
{
    private Request $request;

    private DataSources\DataSource $data_source;

    public function __construct(DataSources\DataSource $data_source)
    {
        $this->data_source = $data_source;
    }

    public function handle(Request $request): void
    {
        $this->request = $request;

        add_filter('acp/data-sources/pre_get_items', [$this, 'send']);
    }

    public function send(QueryBuilder $query): void
    {
        $page = (int)$this->request->filter('ac_page', 1, FILTER_SANITIZE_NUMBER_INT);

        $column = $this->data_source->get_repository()->get_identifier();
        $rows_per_iteration = RowsPerIteration::create($this->request)->apply_filters();

        $query->select($column)
              ->limit(
                  $rows_per_iteration,
                  ($page - 1) * $rows_per_iteration
              );

        $ids = [];

        foreach ($query->all() as $row) {
            $ids[] = $row[$column];
        }

        $response = new Response\QueryRows(
            $ids,
            $rows_per_iteration
        );

        $response->success();
    }

}
