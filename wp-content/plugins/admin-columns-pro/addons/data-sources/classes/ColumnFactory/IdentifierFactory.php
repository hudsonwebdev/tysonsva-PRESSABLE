<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Setting\Config;
use ACA\DataSources\ColumnFactory;
use ACA\DataSources\Sorting\QueryBindings;
use ACP;
use ACP\Sorting\Type\DataType;

final class IdentifierFactory extends ColumnFactory
{
    protected function get_group(): string
    {
        return 'default';
    }

    public function get_column_type(): string
    {
        return $this->get_id();
    }

    protected function get_sorting(Config $config): ACP\Sorting\Model\QueryBindings
    {
        return new QueryBindings($this->get_column_for_query(), DataType::create_numeric());
    }

}
