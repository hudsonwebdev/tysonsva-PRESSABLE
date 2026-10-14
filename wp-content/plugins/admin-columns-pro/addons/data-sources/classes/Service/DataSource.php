<?php

declare(strict_types=1);

namespace ACA\DataSources\Service;

use AC\Message\AdminNotice;
use AC\Registerable;
use ACA\DataSources\DataSourceRegistry;
use ACA\DataSources\Exception\MissingIdentifierException;
use ACA\DataSources\Exception\MultipleIdentifierException;
use ACA\DataSources\Exception\TableNotReadableException;
use ACA\DataSources\ThrowableCollection;
use Throwable;

final class DataSource implements Registerable
{
    private DataSourceRegistry $data_sources;

    public function __construct(DataSourceRegistry $data_sources)
    {
        $this->data_sources = $data_sources;
    }

    public function register(): void
    {
        $throwables = new ThrowableCollection();

        try {
            /**
             * Fires when data sources should be registered.
             *
             * Use the provided DataSourceRegistry instance to register Custom List Tables.
             *
             * Exceptions can either:
             *
             * - Be caught locally and added to the ThrowableCollection,
             *   allowing remaining data sources to continue registering.
             * - Bubble up, which stops the registration process immediately.
             *
             * @param DataSourceRegistry $data_sources Registry used to register data sources
             * @param ThrowableCollection $throwables  Collection for deferred exception handling
             *
             * @since 7.1
             */
            do_action('acp/data-sources/register', $this->data_sources, $throwables);
        } catch (Throwable $t) {
            $this->handle_throwable($t);
        }

        foreach ($throwables as $t) {
            $this->handle_throwable($t);
        }

        foreach ($this->data_sources as $data_source) {
            $screen_option = new ScreenOptions($data_source);
            $screen_option->register();
        }
    }

    private function handle_throwable(Throwable $t): void
    {
        $message[] = sprintf(
            '<strong>%s</strong>',
            __('Failed to register Custom List Table:', 'codepress-admin-columns'),
        );

        switch (true) {
            case $t instanceof MissingIdentifierException:
                $message[] = sprintf(
                    __('Table `%s` is missing an identifier.', 'codepress-admin-columns'),
                    $t->get_table()
                );

                $message[] = __(
                    'Provide an identifier in the configuration, or add a primary key to the table.',
                    'codepress-admin-columns'
                );

                break;
            case $t instanceof MultipleIdentifierException:
                $message[] = sprintf(
                    __('Table `%s` has multiple identifiers. This is not supported.', 'codepress-admin-columns'),
                    $t->get_table()
                );

                $message[] = __(
                    'Provide a single identifier in the configuration.',
                    'codepress-admin-columns'
                );

                break;
            case $t instanceof TableNotReadableException:
                $message[] = sprintf(
                    __('Table `%s` is not readable.', 'codepress-admin-columns'),
                    $t->get_table()
                );

                break;
            default:
                $message[] = $t->getMessage();
        }

        $message = wp_kses(
            implode(' ', $message),
            [
                'strong' => [],
            ]
        );

        (new AdminNotice($message))
            ->set_type('error')
            ->register();
    }

}
