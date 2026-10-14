<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Formatter\ForeignId;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\DataSources\ColumnFactory;
use ACA\DataSources\Editing;
use ACA\DataSources\Metric\Config\DataSourceField;
use ACP;
use ACP\Metric;
use ACP\Metric\ColumnType\Existence;

class WordPressMediaFactory extends ColumnFactory
{
    protected function get_base_formatters(Config $config): FormatterCollection
    {
        return parent::get_base_formatters($config)
            ->add(new ForeignId());
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Media())->set_clear_button(true),
            new Editing\Storage\Column($this->data_source, $this->id)
        );
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new DataSourceField($this->get_column_for_query_result(), new Existence());
    }

}
