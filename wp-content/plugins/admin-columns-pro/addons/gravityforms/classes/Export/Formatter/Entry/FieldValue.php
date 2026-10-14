<?php

declare(strict_types=1);

namespace ACA\GravityForms\Export\Formatter\Entry;

use AC\Exception\ValueNotFoundException;
use AC\Formatter;
use AC\Type\Value;
use ACA\GravityForms\Field\Field;
use GFAPI;

class FieldValue implements Formatter
{
    private Field $field;

    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    public function format(Value $value)
    {
        $entry = GFAPI::get_entry($value->get_id());

        if (! is_array($entry)) {
            throw ValueNotFoundException::from_id($value->get_id());
        }

        // Values spread over multiple entry keys are only readable through the field itself
        $export = $this->field->get_gf_field()->get_value_export($entry, $this->field->get_id(), true, true);

        if (! is_string($export) || '' === trim($export)) {
            throw ValueNotFoundException::from_id($value->get_id());
        }

        return $value->with_value($export);
    }

}
