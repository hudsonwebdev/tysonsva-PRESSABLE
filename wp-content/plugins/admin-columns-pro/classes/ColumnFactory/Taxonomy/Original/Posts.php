<?php

declare(strict_types=1);

namespace ACP\ColumnFactory\Taxonomy\Original;

use AC\Formatter\Term\TermProperty;
use AC\FormatterCollection;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\TaxonomySlug;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;

class Posts extends OriginalColumnFactory
{
    private TaxonomySlug $taxonomy;

    public function __construct(
        FeatureSettingBuilderFactory $feature_settings_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $type,
        string $label,
        TaxonomySlug $taxonomy,
        ?bool $add_sort = true,
        ?bool $add_export = true
    ) {
        parent::__construct(
            $feature_settings_builder_factory,
            $default_settings_builder,
            $type,
            $label,
            $add_sort,
            $add_export
        );

        $this->taxonomy = $taxonomy;
    }

    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new TermProperty('count'));
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new Metric\Config\Taxonomy\PostCount(
            (string)$this->taxonomy,
            ['post'],
            ['publish']
        );
    }

}
