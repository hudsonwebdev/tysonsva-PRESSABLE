<?php

declare(strict_types=1);

namespace ACP\Metric\Service;

use AC\ListScreen;
use AC\Registerable;
use AC\Request;
use AC\TableScreen;
use AC\Type\ColumnId;
use ACP\Column\AdvancedColumn;
use ACP\Metric\PreferenceRepository;
use ACP\Metric\Strategy\AggregateFactory;
use ACP\Metric\Type\AggregationType;

final class Metric implements Registerable
{
    private AggregateFactory $aggregateFactory;

    private PreferenceRepository $preference;

    public function __construct(AggregateFactory $aggregateFactory, PreferenceRepository $preference)
    {
        $this->aggregateFactory = $aggregateFactory;
        $this->preference = $preference;
    }

    public function register(): void
    {
        add_action('ac/table/list_screen', [$this, 'handle'], 15, 2);
    }

    public function handle(ListScreen $list_screen, TableScreen $table_screen): void
    {
        $request = new Request();

        if ('acp_metric_get' !== $request->get('acp_metric_action')) {
            return;
        }

        if (! wp_verify_nonce($request->get('acp_metric_nonce'), 'ac-ajax')) {
            wp_send_json('', 401);
        }

        $column_id = (string)$request->get('acp_metric_column');

        if (! ColumnId::is_valid_id($column_id)) {
            wp_send_json(__('Invalid column ID.', 'codepress-admin-columns'), 400);
        }

        if (! $this->aggregateFactory->supports($table_screen)) {
            wp_send_json(__('Unsupported table screen.', 'codepress-admin-columns'), 400);
        }

        $column = $list_screen->get_column(new ColumnId($column_id));

        if (! $column instanceof AdvancedColumn) {
            wp_send_json(__('Column does not exists.', 'codepress-admin-columns'), 400);
        }

        $aggregation_identifier = (string)$request->get('acp_metric_output');

        if ('' === $aggregation_identifier) {
            $this->preference->delete($list_screen->get_id(), $column->get_id());

            wp_send_json('Preference deleted.', 200);
        }

        $aggregation_type = AggregationType::from_identifier($aggregation_identifier);

        if (null === $aggregation_type) {
            wp_send_json(__('Invalid aggregation type.', 'codepress-admin-columns'), 400);
        }

        $metric = $column->metric();

        if (null === $metric) {
            wp_send_json(__('Columns does not support metrics.', 'codepress-admin-columns'), 400);
        }

        $bindings = $metric->create_query_bindings(
            $aggregation_type
        );

        $this->aggregateFactory
            ->create(
                $table_screen,
                $metric->create_formatter($aggregation_type)
            )
            ->handle($bindings);

        // Save as preference
        $this->preference->save(
            $list_screen->get_id(),
            $column->get_id(),
            $aggregation_type
        );
    }

}
