<?php

declare(strict_types=1);

namespace ACP\ColumnFactory\User;

use AC;
use AC\Setting\Config;
use ACP;
use ACP\Column\EnhancedColumnFactory;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Editing;
use ACP\Metric;
use ACP\Search;
use ACP\Sorting;

class UserUrl extends EnhancedColumnFactory
{
    use ACP\ConditionalFormat\ConditionalFormatTrait;

    private const META_KEY = 'user_url';

    public function __construct(
        AC\ColumnFactory\User\UserUrlFactory $column_factory,
        FeatureSettingBuilderFactory $feature_setting_builder_factory
    ) {
        parent::__construct($column_factory, $feature_setting_builder_factory);
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new Editing\Service\User\Url(__('Website', 'codepress-admin-columns'));
    }

    protected function get_sorting(Config $config): ?Sorting\Model\QueryBindings
    {
        return new Sorting\Model\User\UserField('user_url');
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\Comparison\User\Url();
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new Metric\Config\User\UserField('user_url');
    }

}
