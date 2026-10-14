<?php

declare(strict_types=1);

namespace ACP\Metric;

use AC\TableScreen;

interface StrategyFactory
{
    public function supports(TableScreen $table_screen): bool;

    public function create(TableScreen $table_screen, Formatter $formatter): Strategy;

}
