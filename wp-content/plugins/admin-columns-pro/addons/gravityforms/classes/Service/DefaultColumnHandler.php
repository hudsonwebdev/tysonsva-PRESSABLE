<?php

declare(strict_types=1);

namespace ACA\GravityForms\Service;

use AC;
use AC\ColumnCollection;
use ACA\GravityForms\TableScreen\Entry;
use ACA\GravityForms\Utils\FieldIdsExtractor;

final class DefaultColumnHandler implements AC\DefaultColumnHandler
{
    private FieldIdsExtractor $field_ids_extractor;

    public function __construct(FieldIdsExtractor $field_ids_extractor)
    {
        $this->field_ids_extractor = $field_ids_extractor;
    }

    /**
     * Reduce the given column set to GF's smart-default subset for the form:
     * the starred indicator plus the field columns GF would show by default
     * (first/last name for `name`, first visible input for compounds, simple
     * fields, capped near five, plus entry meta flagged as default).
     *
     * Returning false from `ac/gravityforms/use_form_default_columns` skips the
     * reduction, so the table starts with every available column.
     */
    public function handle(AC\TableScreen $table_screen, ColumnCollection $default_columns): ColumnCollection
    {
        if (! $table_screen instanceof Entry) {
            return $default_columns;
        }

        if (! apply_filters('ac/gravityforms/use_form_default_columns', true, $table_screen)) {
            return $default_columns;
        }

        $allowed_types = $this->build_allowed_types($table_screen->get_form_id());
        $filtered = new ColumnCollection();

        foreach ($default_columns as $column) {
            if (in_array((string)$column->get_type(), $allowed_types, true)) {
                $filtered->add($column);
            }
        }

        return $filtered;
    }

    /**
     * @return array<int, string>
     */
    private function build_allowed_types(int $form_id): array
    {
        $allowed = ['is_starred'];

        foreach ($this->field_ids_extractor->extract_default_columns($form_id) as $field_id) {
            $allowed[] = 'field_id-' . $field_id;
        }

        return $allowed;
    }

}
