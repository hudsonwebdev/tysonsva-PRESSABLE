<?php

declare(strict_types=1);

namespace ACP\Metric;

use AC\Preferences\Preference;
use AC\Preferences\SiteFactory;
use AC\Type\ColumnId;
use AC\Type\ListScreenId;
use ACP\Metric\Type\AggregationType;

class PreferenceRepository
{
    private Preference $storage;

    public function __construct(SiteFactory $preference_factory)
    {
        $this->storage = $preference_factory->create('metric');
    }

    public function save(ListScreenId $list_id, ColumnId $column_id, AggregationType $aggregation_type): void
    {
        $data = $this->storage->find((string)$list_id) ?: [];

        $data[(string)$column_id] = $aggregation_type->get_identifier();

        $this->storage->save(
            (string)$list_id,
            $data
        );
    }

    public function find(ListScreenId $list_id, ColumnId $column_id): ?AggregationType
    {
        static $data = [];

        $list_key = (string)$list_id;

        if (! array_key_exists($list_key, $data)) {
            $data[$list_key] = $this->storage->find($list_key) ?: [];
        }

        $identifier = $data[$list_key][(string)$column_id] ?? '';

        return AggregationType::from_identifier($identifier);
    }

    public function delete(ListScreenId $list_id, ColumnId $column_id): void
    {
        $data = $this->storage->find((string)$list_id) ?: [];

        unset($data[(string)$column_id]);

        $data
            ? $this->storage->save((string)$list_id, $data)
            : $this->storage->delete((string)$list_id);
    }

}
