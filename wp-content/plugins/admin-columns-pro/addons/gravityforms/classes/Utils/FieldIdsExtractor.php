<?php

declare(strict_types=1);

namespace ACA\GravityForms\Utils;

use GF_Field;
use GFAPI;
use GFFormsModel;

final class FieldIdsExtractor
{
    private const UNSUPPORTED_FIELD_TYPES = ['section', 'html', 'page'];

    private const DEFAULT_META_COLUMNS = [
        'id',
        'date_created',
        'ip',
        'source_url',
        'payment_status',
        'transaction_id',
        'payment_amount',
        'payment_date',
        'created_by',
    ];

    /**
     * Return every GF column id available for the form: every field, every
     * sub-input of compound fields, plus the standard entry-meta column names.
     * Used to expand GF's grid column meta during the capture request so AC
     * discovers all columns.
     *
     * @return array<int, int|string>
     */
    public function extract(int $form_id): array
    {
        $form = GFAPI::get_form($form_id);

        if (! is_array($form) || ! isset($form['fields']) || ! is_array($form['fields'])) {
            return self::DEFAULT_META_COLUMNS;
        }

        $ids = [];

        foreach ($form['fields'] as $field) {
            if (! $field instanceof GF_Field) {
                continue;
            }

            if (in_array($field->type, self::UNSUPPORTED_FIELD_TYPES, true)) {
                continue;
            }

            $ids[] = $field->id;

            $inputs = $field->get_entry_inputs();

            if (is_array($inputs)) {
                foreach ($inputs as $input) {
                    if (! isset($input['id'])) {
                        continue;
                    }

                    $ids[] = $input['id'];
                }
            }
        }

        return array_values(array_unique(array_merge($ids, self::DEFAULT_META_COLUMNS)));
    }

    /**
     * Return GF's smart-default column ids for a form: first/last name for
     * `name` fields, the first visible input for other compound fields,
     * simple fields as-is, capped around five field columns, plus entry meta
     * flagged as `is_default_column`. Mirrors GFFormsModel::get_grid_columns().
     *
     * @return array<int, int|string>
     */
    public function extract_default_columns(int $form_id): array
    {
        $form = GFAPI::get_form($form_id);

        if (! is_array($form) || ! isset($form['fields']) || ! is_array($form['fields'])) {
            return $this->get_default_entry_meta_columns($form_id);
        }

        $field_ids = [];

        foreach ($form['fields'] as $field) {
            if (! $field instanceof GF_Field) {
                continue;
            }

            if (in_array($field->type, self::UNSUPPORTED_FIELD_TYPES, true)) {
                continue;
            }

            if (property_exists($field, 'displayOnly') && $field->displayOnly) {
                continue;
            }

            $input_type = method_exists($field, 'get_input_type') ? (string)$field->get_input_type() : '';

            if (in_array($input_type, ['list', 'repeater'], true)) {
                continue;
            }

            $inputs = $field->get_entry_inputs();

            if (is_array($inputs)) {
                if ($field->type === 'name') {
                    $field_ids[] = $field->id . '.3';
                    $field_ids[] = $field->id . '.6';
                } else {
                    foreach ($inputs as $input) {
                        if (! empty($input['isHidden'])) {
                            continue;
                        }

                        if (! isset($input['id'])) {
                            continue;
                        }

                        $field_ids[] = $input['id'];
                        break;
                    }
                }
            } else {
                $field_ids[] = $field->id;
            }

            if (count($field_ids) >= 5) {
                break;
            }
        }

        return array_values(array_unique(array_merge($field_ids, $this->get_default_entry_meta_columns($form_id))));
    }

    /**
     * @return array<int, int|string>
     */
    private function get_default_entry_meta_columns(int $form_id): array
    {
        $entry_metas = GFFormsModel::get_entry_meta($form_id);

        if (! is_array($entry_metas)) {
            return [];
        }

        $defaults = [];

        foreach ($entry_metas as $key => $meta) {
            if (is_array($meta) && ! empty($meta['is_default_column'])) {
                $defaults[] = (string)$key;
            }
        }

        return $defaults;
    }

}
