<?php

declare(strict_types=1);

namespace ACP\Metric\Strategy;

use AC\TableScreen;
use ACP\Metric;
use ACP\Metric\Exception\UnsupportedTableScreenException;
use ACP\Metric\Strategy;
use ACP\Metric\StrategyFactory;

final class AggregateFactory implements StrategyFactory
{
    /**
     * @var StrategyFactory[]
     */
    private array $factories = [];

    public function add(StrategyFactory $factory): void
    {
        array_unshift($this->factories, $factory);
    }

    public function supports(TableScreen $table_screen): bool
    {
        return $this->find($table_screen) !== null;
    }

    public function create(TableScreen $table_screen, Metric\Formatter $formatter): Strategy
    {
        $factory = $this->find($table_screen);

        if ($factory === null) {
            throw new UnsupportedTableScreenException($table_screen);
        }

        return $factory->create($table_screen, $formatter);
    }

    private function find(TableScreen $table_screen): ?StrategyFactory
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($table_screen)) {
                return $factory;
            }
        }

        return null;
    }

}
