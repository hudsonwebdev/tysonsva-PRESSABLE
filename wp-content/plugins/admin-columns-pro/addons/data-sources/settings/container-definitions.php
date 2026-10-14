<?php

namespace ACA\DataSources;

use ACA\DataSources\Setting\ColumnTypeOptionMap;
use ACP\AdminColumnsPro;

use function AC\Vendor\DI\autowire;
use function AC\Vendor\DI\get;

return [
    'addon.data_sources.location'        => static function (AdminColumnsPro $plugin) {
        return $plugin->get_addon_location('data-sources');
    },
    Service\TableScreen::class           => autowire()
        ->constructorParameter(0, get('addon.data_sources.location')),
    ColumnTypeOptionMap::class           => static function () {
        $map = new ColumnTypeOptionMap();

        foreach (require __DIR__ . '/column-type-option-map.php' as $setting) {
            $map->add(...$setting);
        }

        return $map;
    },
    'column_type_factory_map'            => static function () {
        $map = new ColumnFactoryMap();

        foreach (require __DIR__ . '/column-type-factory-map.php' as $column_reference => $factory) {
            $map->add($column_reference, $factory);
        }

        return $map;
    },
    'column_relation_factory_map'        => static function () {
        $map = new ColumnFactoryMap();

        foreach (require __DIR__ . '/column-relation-factory-map.php' as $column_reference => $factory) {
            $map->add($column_reference, $factory);
        }

        return $map;
    },
    ColumnFactoryResolver::class         => autowire()
        ->constructorParameter(1, get('column_type_factory_map')),
    RelationColumnFactoryResolver::class => autowire()
        ->constructorParameter(1, get('column_relation_factory_map')),
    CapabilitiesChecker::class           => static function () {
        return new CapabilitiesChecker(
            (new CapabilitiesFactory\Base())->create()
        );
    },
];
