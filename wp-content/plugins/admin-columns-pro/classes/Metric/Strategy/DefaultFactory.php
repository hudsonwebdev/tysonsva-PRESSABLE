<?php

declare(strict_types=1);

namespace ACP\Metric\Strategy;

use AC\TableScreen;
use ACP\Metric;
use ACP\Metric\Exception\UnsupportedTableScreenException;
use ACP\Metric\Strategy;
use ACP\Metric\StrategyFactory;

/**
 * Creates a metric Strategy for any Table Screen that is an instance of a given type.
 */
final class DefaultFactory implements StrategyFactory
{
    /**
     * @var class-string<TableScreen>
     */
    private string $table_screen;

    /**
     * @var class-string<Strategy>
     */
    private string $strategy;

    /**
     * @param class-string<TableScreen> $table_screen Class or interface a screen must be an instance of
     * @param class-string<Strategy>    $strategy     Strategy to instantiate (constructed with the formatter)
     */
    public function __construct(string $table_screen, string $strategy)
    {
        $this->table_screen = $table_screen;
        $this->strategy = $strategy;
    }

    public function supports(TableScreen $table_screen): bool
    {
        $type = $this->table_screen;

        return $table_screen instanceof $type;
    }

    public function create(TableScreen $table_screen, Metric\Formatter $formatter): Strategy
    {
        if (! $this->supports($table_screen)) {
            throw new UnsupportedTableScreenException($table_screen);
        }

        $strategy = $this->strategy;

        return new $strategy($formatter);
    }

}
