<?php

declare(strict_types=1);

namespace ACA\DataSources\Filtering\Table;

use AC;
use ACP\Filtering\View\FilterContainer;

class DataSource implements AC\Registerable
{
    private string $column_name;

    public function __construct(string $column_name)
    {
        $this->column_name = $column_name;
    }

    public function register(): void
    {
        add_action('acp/data-sources/restrict_items', [$this, 'render'], 11);
    }

    public function render(): void
    {
        echo new FilterContainer($this->column_name);
    }

}
