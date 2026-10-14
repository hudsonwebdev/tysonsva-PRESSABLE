<?php

declare(strict_types=1);

namespace ACA\DataSources;

use AC;
use AC\ListTable\TotalItemsTrait;
use AC\Type\Labels;
use AC\Type\TableId;
use AC\Type\Uri;

final class TableScreen extends AC\TableScreen implements AC\TableScreen\TotalItems, AC\TableScreen\ListTable
{
    use TotalItemsTrait;

    private DataSource $data_source;

    private ListTable $list_table;

    public function __construct(
        TableId $id,
        DataSource $data_source,
        ListTable $list_table,
        string $screen_id,
        Labels $labels,
        Uri $uri
    ) {
        $this->data_source = $data_source;
        $this->list_table = $list_table;

        parent::__construct($id, $screen_id, $labels, $uri);
    }

    public function get_data_source(): DataSource
    {
        return $this->data_source;
    }

    public function list_table(): ListTable
    {
        return $this->list_table;
    }

}
