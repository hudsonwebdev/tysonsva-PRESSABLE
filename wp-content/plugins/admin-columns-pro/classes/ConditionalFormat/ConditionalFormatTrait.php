<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat;

use AC\Setting\Config;

trait ConditionalFormatTrait
{
    protected function get_conditional_format(Config $config): ?FormattableConfig
    {
        return new FormattableConfig(
            new Formatter\FilterHtmlFormatter()
        );
    }

}
