<?php

declare(strict_types=1);

namespace ACA\SeoPress\ColumnFactory\Post;

use AC;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACP;
use ACP\Metric;
use ACP\Metric\Config\Meta\Numeric;
use ACP\Sorting\Type\DataType;

class MetaNumberStatFactory extends MetaFactory
{
    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)
                     ->add(new AC\Formatter\StripTags())
                     ->add(new AC\Formatter\FallBack('0'));
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Meta\Number($this->get_meta_key());
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key(), new DataType(DataType::NUMERIC));
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Numeric::for_post($this->get_meta_key());
    }

}
