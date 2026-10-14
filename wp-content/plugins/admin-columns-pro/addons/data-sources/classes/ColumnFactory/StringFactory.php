<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Setting\Config;
use ACA\DataSources\ColumnFactory;
use ACA\DataSources\Editing;
use ACA\DataSources\Metric\Config\DataSourceField;
use ACA\DataSources\Search\Comparison;
use ACA\DataSources\Sorting\QueryBindings;
use ACP;
use ACP\Metric;
use ACP\Metric\ColumnType\Existence;
use ACP\Sorting\Type\DataType;

abstract class StringFactory extends ColumnFactory
{
    use ACP\ConditionalFormat\ConditionalFormatTrait;

    protected function get_sorting(Config $config): ACP\Sorting\Model\QueryBindings
    {
        return new QueryBindings($this->get_column_for_query(), DataType::create_string());
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Comparison\TextComparison($this->get_column_for_query());
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Text())->set_clear_button(true),
            new Editing\Storage\Column($this->data_source, $this->id)
        );
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new DataSourceField($this->get_column_for_query_result(), new Existence());
    }

}
