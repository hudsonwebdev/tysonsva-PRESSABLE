<?php

declare(strict_types=1);

namespace ACP\Metric;

use AC;
use AC\Asset\Location;
use AC\Registerable;
use AC\Services;
use AC\TableScreen;
use AC\Vendor\DI\Container;
use ACP;
use ACP\AdminColumnsPro;
use ACP\Metric\Service\Metric;
use ACP\Metric\Service\Scripts;
use ACP\Metric\Strategy\AggregateFactory;

final class Addon implements Registerable
{
    private Location\Absolute $location;

    private Container $container;

    public function __construct(AdminColumnsPro $plugin, Container $container)
    {
        $this->location = $plugin->get_location();
        $this->container = $container;
    }

    public function register(): void
    {
        // Factories
        $aggregate_factory = $this->container->get(AggregateFactory::class);
        $aggregate_factory->add(new Strategy\DefaultFactory(AC\PostType::class, Strategy\Post::class));
        $aggregate_factory->add(new Strategy\DefaultFactory(TableScreen\User::class, Strategy\User::class));
        $aggregate_factory->add(new Strategy\DefaultFactory(ACP\TableScreen\Taxonomy::class, Strategy\Taxonomy::class));
        $aggregate_factory->add(new Strategy\DefaultFactory(TableScreen\Comment::class, Strategy\Comment::class));

        // Services
        $services[] = $this->container->get(Metric::class);
        $services[] = $this->container->get(Service\Admin::class);
        $services[] = $this->container->make(Scripts::class, ['location' => $this->location]);

        (new Services($services))->register();
    }

}
