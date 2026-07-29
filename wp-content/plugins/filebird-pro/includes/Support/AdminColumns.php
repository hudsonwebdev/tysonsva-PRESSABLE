<?php

namespace FileBird\Support;

use FileBird\Support\AdminColumns\FileBirdColumn;

defined('ABSPATH') || exit;

// use Filebird\Support\AdminColumns\Column;
class AdminColumns
{
	public function __construct()
	{
		// add_action( 'acp/column_types', array( $this, 'register_column_type' ) );
		add_filter('ac/column/types/pro', array($this, 'register_column_type'), 10, 2);
	}

	public function register_column_type($factories, $table_screen)
	{
		// Only add column for Media screen
		if ((string) $table_screen->get_id() !== 'wp-media') {
			return $factories;
		}

		// Include custom column classes
		require_once __DIR__ . '/AdminColumns/FileBirdFormatter.php';
		require_once __DIR__ . '/AdminColumns/FileBirdColumn.php';

		// Register the custom FileBird Column
		$factories[] = FileBirdColumn::class;

		return $factories;
	}
}