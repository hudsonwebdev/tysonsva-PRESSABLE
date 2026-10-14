<?php

declare(strict_types=1);

namespace ACA\GravityForms\Service;

use AC\ListScreen;
use ACA\GravityForms\TableScreen;
use ACA\GravityForms\Utils\ActiveListScreenResolver;
use ACA\GravityForms\Utils\FieldIdsExtractor;
use GFFormsModel;

final class GridColumnMetaScope
{
    private FieldIdsExtractor $field_ids_extractor;

    private ActiveListScreenResolver $list_screen_resolver;

    /**
     * @var array<int, mixed>
     */
    private array $snapshots = [];

    public function __construct(
        FieldIdsExtractor $field_ids_extractor,
        ActiveListScreenResolver $list_screen_resolver
    ) {
        $this->field_ids_extractor = $field_ids_extractor;
        $this->list_screen_resolver = $list_screen_resolver;
    }

    /**
     * Snapshot the current grid column meta and replace it with every field id
     * (full discovery set). The snapshot is kept so {@see restore_to_baseline}
     * can fall back to it when no AC list screen exists yet.
     */
    public function activate_all_columns(TableScreen\Entry $table_screen): void
    {
        $form_id = $table_screen->get_form_id();

        $this->snapshots[$form_id] = GFFormsModel::get_grid_column_meta($form_id);

        $this->write_if_changed($form_id, $this->field_ids_extractor->extract($form_id));
    }

    /**
     * Write the active AC list screen's columns back to grid column meta.
     * Falls back to the pre-activation snapshot when no list screen exists yet
     * (typical for a freshly seen form during the first capture request).
     */
    public function restore_to_baseline(TableScreen\Entry $table_screen): void
    {
        $form_id = $table_screen->get_form_id();

        $active = $this->list_screen_resolver->resolve($table_screen);

        if ($active) {
            $this->write_if_changed($form_id, $this->column_ids_from_list_screen($active));

            return;
        }

        if (array_key_exists($form_id, $this->snapshots)) {
            $snapshot = $this->snapshots[$form_id];

            if (is_array($snapshot)) {
                $this->write_if_changed($form_id, $snapshot);
            }
        }
    }

    /**
     * Mirror the given list screen's GF column ids into grid column meta.
     */
    public function write_for_list_screen(TableScreen\Entry $table_screen, ListScreen $list_screen): void
    {
        $this->write_if_changed(
            $table_screen->get_form_id(),
            $this->column_ids_from_list_screen($list_screen)
        );
    }

    /**
     * @param array<int, int|string> $column_ids
     */
    private function write_if_changed(int $form_id, array $column_ids): void
    {
        $current = GFFormsModel::get_grid_column_meta($form_id);
        $current_normalised = $this->normalise(is_array($current) ? $current : []);
        $next_normalised = $this->normalise($column_ids);

        if ($current_normalised === $next_normalised) {
            return;
        }

        GFFormsModel::update_grid_column_meta($form_id, $column_ids);
    }

    /**
     * Cast all ids to strings and reindex so equivalence is independent of
     * int/string key types (GF returns mixed types from the database).
     *
     * @param array<int|string, mixed> $ids
     *
     * @return array<int, string>
     */
    private function normalise(array $ids): array
    {
        return array_values(array_map('strval', $ids));
    }

    /**
     * @return array<int, int|string>
     */
    private function column_ids_from_list_screen(ListScreen $list_screen): array
    {
        $ids = [];

        foreach ($list_screen->get_columns() as $column) {
            $type = (string)$column->get_type();

            if (strpos($type, 'field_id-') !== 0) {
                continue;
            }

            $ids[] = substr($type, strlen('field_id-'));
        }

        return array_values(array_unique($ids));
    }

}
