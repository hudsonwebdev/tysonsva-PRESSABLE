<?php

declare(strict_types=1);

namespace ACP\ColumnFactory\User;

use AC\ColumnFactory\User\LastNameFactory;
use AC\Formatter\User\Meta;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACP;
use ACP\Column\EnhancedColumnFactory;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Editing;
use ACP\Metric;
use ACP\Search;
use ACP\Sorting;

class LastName extends EnhancedColumnFactory
{
    use ACP\ConditionalFormat\ConditionalFormatTrait;

    private const META_KEY = 'last_name';

    public function __construct(
        LastNameFactory $column_factory,
        FeatureSettingBuilderFactory $feature_setting_builder_factory
    ) {
        parent::__construct($column_factory, $feature_setting_builder_factory);
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new Editing\Service\Basic(
            (new Editing\View\Text())->set_clear_button(true),
            new Editing\Storage\User\Meta(self::META_KEY)
        );
    }

    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new Meta(self::META_KEY));
    }

    protected function get_sorting(Config $config): ?Sorting\Model\QueryBindings
    {
        return new Sorting\Model\User\Meta(self::META_KEY);
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\Comparison\Meta\Text(self::META_KEY);
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Text::for_user(self::META_KEY);
    }

}
