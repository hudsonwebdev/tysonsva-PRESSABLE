<?php

declare(strict_types=1);

namespace ACA\GravityForms\Service;

use AC;
use AC\ColumnTypeRepository;
use AC\DefaultColumnHandler;
use AC\ListScreen;
use AC\ListScreenRepository\Storage;
use AC\Registerable;
use AC\Storage\Repository\OriginalColumnsRepository;
use ACA\GravityForms\TableScreen;

final class StoreOriginalColumns implements Registerable
{
    private OriginalColumnsRepository $original_columns_repository;

    private Storage $storage;

    private AC\Type\ListScreenIdGenerator $list_screen_id_generator;

    private ColumnTypeRepository $column_type_repository;

    private DefaultColumnHandler $default_column_handler;

    public function __construct(
        OriginalColumnsRepository $original_columns_repository,
        Storage $storage,
        AC\Type\ListScreenIdGenerator $list_screen_id_generator,
        ColumnTypeRepository $column_type_repository,
        DefaultColumnHandler $default_column_handler
    ) {
        $this->original_columns_repository = $original_columns_repository;
        $this->storage = $storage;
        $this->list_screen_id_generator = $list_screen_id_generator;
        $this->column_type_repository = $column_type_repository;
        $this->default_column_handler = $default_column_handler;
    }

    public function register(): void
    {
        add_action('ac/table/screen', [$this, 'create_default_list_screen'], 9);
    }

    /**
     * Seed an initial AC list screen for a GF entry table the first time AC
     * encounters it with original columns available. Idempotent: skips when
     * any list screen already exists for the table.
     */
    public function create_default_list_screen(AC\TableScreen $table_screen): void
    {
        if (! $table_screen instanceof TableScreen\Entry) {
            return;
        }

        if (! apply_filters('ac/gravityforms/create_default_set', true)) {
            return;
        }

        $table_id = $table_screen->get_id();

        if (! $this->original_columns_repository->exists($table_id)) {
            return;
        }

        if ($this->storage->find_all_by_table_id($table_id)->count() > 0) {
            return;
        }

        $all_originals = $this->column_type_repository->find_all_original($table_screen);
        $defaults = $this->default_column_handler->handle($table_screen, $all_originals);

        $this->storage->save(
            new ListScreen(
                $this->list_screen_id_generator->generate(),
                __('Default', 'codepress-admin-columns'),
                $table_screen,
                $defaults
            )
        );
    }

}
