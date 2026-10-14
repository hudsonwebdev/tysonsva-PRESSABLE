<?php

declare(strict_types=1);

namespace ACP\Metric\Setting\ComponentFactory;

use AC\Setting\AttributeCollection;
use AC\Setting\AttributeFactory;
use AC\Setting\ComponentFactory\BaseComponentFactory;
use AC\Setting\Config;
use AC\Setting\Control\Input;
use AC\Setting\Control\Input\OptionFactory;

class Metric extends BaseComponentFactory
{
    protected function get_label(Config $config): ?string
    {
        return __('Enable Metrics', 'codepress-admin-columns');
    }

    protected function get_input(Config $config): ?Input
    {
        return OptionFactory::create_toggle('metrics', null, $config->has('metrics') ? $config->get('metrics') : 'on');
    }

    protected function get_attributes(Config $config, AttributeCollection $attributes): AttributeCollection
    {
        return new AttributeCollection([
            AttributeFactory::create_help_reference('doc-metrics'),
        ]);
    }
}
