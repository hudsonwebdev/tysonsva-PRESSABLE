<?php

declare(strict_types=1);

namespace ACP\Metric\Type;

use InvalidArgumentException;

final class AggregationType
{
    public const SUM = 'SUM';
    public const MIN = 'MIN';
    public const MAX = 'MAX';
    public const AVG = 'AVG';
    public const RANGE = 'RANGE';
    public const IS_EMPTY = 'IS_EMPTY';
    public const IS_EMPTY_PERCENTAGE = 'IS_EMPTY_PERCENTAGE';
    public const IS_NOT_EMPTY = 'IS_NOT_EMPTY';
    public const IS_NOT_EMPTY_PERCENTAGE = 'IS_NOT_EMPTY_PERCENTAGE';
    public const DATE_RANGE = 'DATE_RANGE';
    public const DATE_MIN = 'DATE_MIN';
    public const DATE_MAX = 'DATE_MAX';
    public const COUNT = 'COUNT';
    public const COUNT_PERCENTAGE = 'COUNT_PERCENTAGE';

    private string $aggregation;

    private ?string $context;

    private ?string $label;

    public function __construct(string $aggregation, ?string $context = null, ?string $label = null)
    {
        if (! self::validate($aggregation)) {
            throw new InvalidArgumentException('Invalid aggregation type: ' . $aggregation);
        }

        if ($context === null && self::requires_context($aggregation)) {
            throw new InvalidArgumentException(
                sprintf('Aggregation type "%s" requires a context.', $aggregation)
            );
        }

        $this->aggregation = $aggregation;
        $this->context = $context;
        $this->label = $label;
    }

    public static function validate(string $aggregation): bool
    {
        $valid_types = [
            self::SUM,
            self::MIN,
            self::MAX,
            self::AVG,
            self::RANGE,
            self::DATE_RANGE,
            self::DATE_MIN,
            self::DATE_MAX,
            self::IS_EMPTY,
            self::IS_NOT_EMPTY,
            self::IS_EMPTY_PERCENTAGE,
            self::IS_NOT_EMPTY_PERCENTAGE,
            self::COUNT,
            self::COUNT_PERCENTAGE,
        ];

        return in_array($aggregation, $valid_types, true);
    }

    public static function requires_context(string $aggregation): bool
    {
        return in_array($aggregation, [
            self::COUNT,
            self::COUNT_PERCENTAGE,
        ], true);
    }

    public static function from_identifier(string $identifier): ?self
    {
        if (strpos($identifier, ':') !== false) {
            [$type, $context] = explode(':', $identifier, 2);

            return self::validate($type) ? new self($type, $context) : null;
        }

        if (! self::validate($identifier) || self::requires_context($identifier)) {
            return null;
        }

        return new self($identifier);
    }

    public function get_context(): ?string
    {
        return $this->context;
    }

    public function get_label(): ?string
    {
        return $this->label;
    }

    public function get_identifier(): string
    {
        return $this->context !== null
            ? $this->aggregation . ':' . $this->context
            : $this->aggregation;
    }

    public function get_value(): string
    {
        return $this->aggregation;
    }

    public function is_percentage(): bool
    {
        return in_array($this->aggregation, [
            self::COUNT_PERCENTAGE,
            self::IS_EMPTY_PERCENTAGE,
            self::IS_NOT_EMPTY_PERCENTAGE,
        ], true);
    }

    public function is_existence(): bool
    {
        return in_array($this->aggregation, [
            self::IS_EMPTY,
            self::IS_EMPTY_PERCENTAGE,
            self::IS_NOT_EMPTY,
            self::IS_NOT_EMPTY_PERCENTAGE,
        ], true);
    }

    public function __toString(): string
    {
        return $this->aggregation;
    }
}
