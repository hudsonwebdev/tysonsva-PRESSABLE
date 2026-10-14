<?php

declare(strict_types=1);

namespace ACP\ColumnFactory\Media;

use AC;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACP\Column\EnhancedColumnFactory;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\Editing;
use ACP\Metric;
use ACP\Search;
use ACP\Sorting;

class AlternateText extends EnhancedColumnFactory
{
    use ConditionalFormatTrait;

    private const META_KEY = '_wp_attachment_image_alt';

    public function __construct(
        AC\ColumnFactory\Media\AlternateTextFactory $column_factory,
        FeatureSettingBuilderFactory $feature_setting_builder_factory
    ) {
        parent::__construct($column_factory, $feature_setting_builder_factory);
    }

    protected function get_editing(Config $config): ?Editing\Service
    {
        return new Editing\Service\Media\AlternateText();
    }

    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new AC\Formatter\Post\Meta(self::META_KEY));
    }

    protected function get_sorting(Config $config): ?Sorting\Model\QueryBindings
    {
        return new Sorting\Model\Post\Meta(self::META_KEY);
    }

    protected function get_search(Config $config): ?Search\Comparison
    {
        return new Search\Comparison\Meta\Text(self::META_KEY);
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Text::for_post(self::META_KEY);
    }

}
