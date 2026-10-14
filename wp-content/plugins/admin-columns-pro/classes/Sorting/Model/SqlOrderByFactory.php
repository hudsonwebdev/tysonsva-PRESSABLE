<?php

declare(strict_types=1);

namespace ACP\Sorting\Model;

use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\EmptyValues;
use ACP\Sql\Identifier;

class SqlOrderByFactory
{
    public static function create_with_computation(
        ComputationType $computation,
        string $field,
        string $order,
        bool $include_zero = false
    ): string {
        $field = sprintf('%s( %s )', $computation, $field);

        if ('DESC' === $order) {
            return sprintf('%s %s', $field, $order);
        }

        $empty_values[] = null;

        if ($include_zero) {
            $empty_values[] = 0;
        }

        return self::create($field, $order, [
            'quote'        => false,
            'empty_values' => $empty_values,
        ]);
    }

    public static function create_with_count(string $field, string $order): string
    {
        return self::create_with_computation(
            new ComputationType(ComputationType::COUNT),
            $field,
            $order,
            true
        );
    }

    public static function create_with_ids(string $field, array $ids, string $order): string
    {
        return self::create_with_field(
            $field,
            $ids,
            $order,
            new DataType(DataType::NUMERIC)
        );
    }

    public static function create_with_field(
        string $field,
        array $values,
        string $order,
        ?DataType $data_type = null
    ): string {
        if (null === $data_type) {
            $data_type = new DataType(DataType::STRING);
        }

        $values = self::sanitize_values($values, $data_type);

        if (! $values) {
            return '';
        }

        if ('ASC' === $order) {
            $values = array_reverse($values);
        }

        return sprintf(
            'FIELD(%1$s, %2$s) DESC',
            $field,
            implode(',', self::esc_sql_values($values, $data_type))
        );
    }

    private static function wrap_single_quote($value): string
    {
        return sprintf("'%s'", $value);
    }

    private static function esc_sql_values(array $values, DataType $data_type): array
    {
        switch ($data_type->get_value()) {
            case DataType::NUMERIC:
                return array_map('esc_sql', $values);
            default:
                return array_map([__CLASS__, 'wrap_single_quote'], array_map('esc_sql', $values));
        }
    }

    private static function sanitize_values(array $values, DataType $data_type): array
    {
        switch ($data_type->get_value()) {
            case DataType::NUMERIC:
                return array_filter($values, 'is_int');
            default:
                return array_filter($values, 'is_scalar');
        }
    }

    // TODO David maybe this is better as an expression instead of named constructor?
    /**
     * @param array<string> $fields
     */
    public static function create_with_concat(array $fields, string $order, ?CastType $cast_type = null): string
    {
        $field = sprintf('CONCAT(%s)', implode(', ', $fields));
        $args = [
            'quote' => false,
        ];

        if ($cast_type !== null) {
            $args['cast_type'] = (string)$cast_type;
        }

        return self::create($field, $order, $args);
    }

    public static function create(string $field, string $order, array $args = []): string
    {
        $args = array_merge([
            'empty_values' => [null, ''],
            'cast_type'    => null,
            'esc_sql'      => true,
            'quote'        => true,
        ], $args);

        $empty_values = new EmptyValues($args['empty_values']);
        $cast_type = $args['cast_type'] ? new CastType($args['cast_type']) : null;

        // TODO David check when required call-wise and if it aligns with quote
        if ($args['esc_sql']) {
            $field = (string)esc_sql($field);
        }

        // TODO David make sure callers implement this properly
        if ($args['quote']) {
            $field = Identifier::quote($field);
        }

        // TODO David check if there is alignment with generic SQL Cast expression
        $order_by = sprintf(
            '%s %s',
            $cast_type ? sprintf("CAST( %s AS %s )", $field, $cast_type) : $field,
            $order
        );

        if ('DESC' === $order) {
            return $order_by;
        }

        $when_parts = [];

        if ($empty_values->has_value(EmptyValues::NULL)) {
            $when_parts[] = sprintf('WHEN %s IS null THEN 1', $field);
        }
        if ($empty_values->has_value(EmptyValues::EMPTY_STRING)) {
            $when_parts[] = sprintf('WHEN %s = \'\' THEN 1', $field);
        }
        if ($empty_values->has_value(EmptyValues::ZERO)) {
            $when_parts[] = sprintf('WHEN %s = 0 THEN 1', $field);
        }
        if ($empty_values->has_value(EmptyValues::LTE_ZERO)) {
            $when_parts[] = sprintf('WHEN %s <= 0 THEN 1', $field);
        }

        if (! $when_parts) {
            return $order_by;
        }

        return sprintf(
            '
			CASE 
				%s
				ELSE 0 END
				, %s
			',
            implode("\n", $when_parts),
            $order_by
        );
    }

}
