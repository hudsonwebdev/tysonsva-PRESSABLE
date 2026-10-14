<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\FormatValue;
use ACP\Sql\QueryBuilder;

trait PostResultsTrait
{
    use PostRequestTrait;

    protected array $db_columns = [];

    protected ?FormatValue $formatter = null;

    protected ?int $max_value_length = 100;

    protected bool $sort_numeric = false;

    public function get_post_ids(): array
    {
        $ids = [];

        foreach ($this->get_query_results() as $row) {
            $ids[$row->id] = $this->format_rows($row);
        }

        $ids = array_filter($ids);

        $this->sort_numeric
            ? asort($ids, SORT_NUMERIC)
            : natcasesort($ids);

        return array_keys($ids);
    }

    private function format_rows(object $row): string
    {
        $value = '';

        foreach ($this->db_columns as $column) {
            $raw = $row->{$column} ?? null;
            $value .= $this->formatter
                ? (string)$this->formatter->format_value($raw)
                : (string)$raw;
        }

        return $value;
    }

    private function get_query_results(): array
    {
        global $wpdb;

        $select = ["$wpdb->posts.ID AS id"];

        foreach ($this->db_columns as $column) {
            $escaped = esc_sql($column);
            $select[] = $this->max_value_length > 0
                ? 'LEFT( ' . $wpdb->posts . '.' . $escaped . ', ' . $this->max_value_length . ' ) AS ' . $escaped
                : $wpdb->posts . '.' . $escaped . ' AS ' . $escaped;
        }

        $query = QueryBuilder::for($wpdb->posts)
            ->select_columns($select)
            ->where("$wpdb->posts.post_type", '=', $this->get_var_post_type());

        $status = $this->get_var_post_status();
        if ($status) {
            $query->and_where("$wpdb->posts.post_status", '=', $status);
        }

        return $wpdb->get_results($query->get_sql());
    }
}
