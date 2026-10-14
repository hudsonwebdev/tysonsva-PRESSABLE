<?php

declare(strict_types=1);

namespace ACA\DataSources;

use AC;
use AC\DI\Container;
use AC\Table\ManageValue\ListScreenServiceFactory;
use ACA;
use ACA\DataSources\ColumnFactories\DataSourceColumns;
use ACA\DataSources\Query\QueryFactory;
use ACP;
use ACP\Addon;

final class DataSources implements Addon
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get_id(): string
    {
        return 'data-sources';
    }

    public function register(): void
    {
        $services = [
            Service\Capabilities::class,
            Service\ColumnGroups::class,
            Service\DataSource::class,
            Service\Page::class,
            Service\TableScreen::class,
        ];

        foreach ($services as $service) {
            $instance = $this->container->get($service);
            $instance->register();
        }

        // Setup default column handling
        $default_column_handler = $this->container->get(AC\DefaultColumnHandler::class);

        if ($default_column_handler instanceof AC\DefaultColumnHandler\Aggregate) {
            $default_column_handler->add($this->container->get(DefaultColumnHandler::class));
        }

        AC\ColumnFactories\Aggregate::add($this->container->get(DataSourceColumns::class));

        AC\Service\ManageHeadings::add($this->container->get(ListTable\ManageHeading\DataSourceFactory::class));
        AC\Service\SaveHeadings::add($this->container->get(ListTable\SaveHeading\DataSourceFactory::class));

        AC\Service\ManageValue::add(
            $this->container->make(
                ListScreenServiceFactory::class,
                ['factory' => $this->container->get(ListTable\ManageValue\DataSourceServiceFactory::class)]
            )
        );

        AC\Service\ManageValue::add(
            $this->container->make(
                ACP\ConditionalFormat\ManageValue\RenderableServiceFactory::class,
                ['factory' => $this->container->get(ListTable\ManageValue\DataSourceServiceFactory::class)]
            )
        );

        AC\TableScreen\TableRowsFactory\Aggregate::add(
            $this->container->get(ACA\DataSources\TableScreen\TableRowsFactory::class)
        );

        ACP\Query\QueryRegistry::add($this->container->get(QueryFactory::class));

        ACP\Search\TableMarkupFactory::register(
            ACA\DataSources\TableScreen::class,
            ACA\DataSources\Search\TableScreen\DataSource::class
        );

        ACP\Filtering\TableScreenFactory::register(
            ACA\DataSources\TableScreen::class,
            ACA\DataSources\Filtering\Table\DataSource::class
        );

        ACP\Editing\BulkDelete\AggregateFactory::add(
            $this->container->get(BulkDelete\Deletable\DataSourceFactory::class)
        );
        ACP\Editing\Strategy\AggregateFactory::add($this->container->get(Editing\Strategy\DataSourceFactory::class));
        ACP\Export\Strategy\AggregateFactory::add($this->container->get(Export\Strategy\DataSourceFactory::class));
        $this->container->get(ACP\Metric\Strategy\AggregateFactory::class)->add(
            new ACP\Metric\Strategy\DefaultFactory(TableScreen::class, Metric\Strategy\DataSource::class)
        );
        AC\TableIdsFactory\Aggregate::add($this->container->get(ACA\DataSources\TableScreen\TableIdsFactory::class));
        AC\TableScreenFactory\Aggregate::add($this->container->get(TableScreenFactory::class));
        AC\Admin\MenuGroupFactory\Aggregate::add(
            $this->container->get(ACA\DataSources\TableScreen\MenuGroupFactory::class)
        );
    }

}
