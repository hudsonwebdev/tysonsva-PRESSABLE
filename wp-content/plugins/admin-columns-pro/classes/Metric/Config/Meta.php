<?php

declare(strict_types=1);

namespace ACP\Metric\Config;

use AC\MetaType;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;

abstract class Meta extends Base
{
    private string $meta_key;

    private string $table;

    private string $id;

    private string $meta_table;

    private string $meta_id;

    protected ?AggregationTypes $metrics;

    private ?Metric\FormatterFactory $formatter_factory = null;

    private ?AggregationType $aggregation_type = null;

    public function __construct(
        string $meta_key,
        string $table,
        string $id,
        string $meta_table,
        string $meta_id,
        ?AggregationTypes $metrics = null
    ) {
        $this->meta_key = $meta_key;
        $this->table = $table;
        $this->id = $id;
        $this->meta_table = $meta_table;
        $this->meta_id = $meta_id;
        $this->metrics = $metrics;
    }

    public function with_formatter(Metric\FormatterFactory $formatter_factory): self
    {
        $clone = clone $this;
        $clone->formatter_factory = $formatter_factory;

        return $clone;
    }

    abstract protected function get_column_expression(string $alias): string;

    abstract public function get_aggregation_types(): AggregationTypes;

    abstract protected function create_default_formatter(AggregationType $aggregation_type): Metric\Formatter;

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        if ($this->formatter_factory) {
            return $this->formatter_factory->create($aggregation_type);
        }

        return $this->create_default_formatter($aggregation_type);
    }

    /**
     * @return static
     */
    public static function from_meta_type(
        MetaType $meta_type,
        string $meta_key,
        ?AggregationTypes $metrics = null
    ): ?self {
        switch ((string)$meta_type) {
            case MetaType::POST:
                return static::for_post($meta_key, $metrics);
            case MetaType::USER:
                return static::for_user($meta_key, $metrics);
            case MetaType::COMMENT:
                return static::for_comment($meta_key, $metrics);
            case MetaType::TERM:
                return static::for_term($meta_key, $metrics);
            default:
                return null;
        }
    }

    /**
     * @return static
     */
    public static function for_user(
        string $meta_key,
        ?AggregationTypes $metrics = null
    ): self {
        global $wpdb;

        return new static(
            $meta_key,
            $wpdb->users,
            'ID',
            $wpdb->usermeta,
            'user_id',
            $metrics
        );
    }

    /**
     * @return static
     */
    public static function for_term(
        string $meta_key,
        ?AggregationTypes $metrics = null
    ): self {
        global $wpdb;

        return new static(
            $meta_key,
            $wpdb->terms,
            'term_id',
            $wpdb->termmeta,
            'term_id',
            $metrics
        );
    }

    /**
     * @return static
     */
    public static function for_comment(
        string $meta_key,
        ?AggregationTypes $metrics = null
    ): self {
        global $wpdb;

        return new static(
            $meta_key,
            $wpdb->comments,
            'comment_ID',
            $wpdb->commentmeta,
            'comment_id',
            $metrics
        );
    }

    /**
     * @return static
     */
    public static function for_post(
        string $meta_key,
        ?AggregationTypes $metrics = null
    ): self {
        global $wpdb;

        return new static(
            $meta_key,
            $wpdb->posts,
            'ID',
            $wpdb->postmeta,
            'post_id',
            $metrics
        );
    }

    /**
     * @return static
     */
    public static function for_order(
        string $meta_key,
        ?AggregationTypes $metrics = null
    ): self {
        global $wpdb;

        return new static(
            $meta_key,
            $wpdb->prefix . 'wc_orders',
            'id',
            $wpdb->prefix . 'wc_orders_meta',
            'order_id',
            $metrics
        );
    }

    private function requires_left_join(AggregationType $aggregation_type): bool
    {
        return in_array($aggregation_type->get_value(), [
            AggregationType::IS_EMPTY,
            AggregationType::IS_EMPTY_PERCENTAGE,
            AggregationType::IS_NOT_EMPTY_PERCENTAGE,
        ], true);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $this->aggregation_type = $aggregation_type;

        $join = $this->requires_left_join($aggregation_type)
            ? Join::left($this->meta_table, $alias)
            : Join::inner($this->meta_table, $alias);

        $join
            ->on("{$this->table}.{$this->id}", '=', "{$alias}.{$this->meta_id}")
            ->on("{$alias}.meta_key", '=', $wpdb->prepare('%s', $this->meta_key));

        $bindings->join($join->get_sql());
    }

    protected function get_column(string $alias): string
    {
        if ($this->aggregation_type !== null && $this->aggregation_type->is_existence()) {
            return sprintf('%s.meta_value', $alias);
        }

        return $this->get_column_expression($alias);
    }

}
