<?php
$white = "#ffffff";
$black = "#000000";
$darkPurple = "#0C0042";
$purple = "#260D66";
$steelBlue = "#385DFF";
$gray = "#315372";
$skyBlue = "#9ADCF7";
$green = "#00B852";
$tint = "#E5ECF2";

// Adds support for editor color palette.
add_theme_support( 'editor-color-palette', array(
	array(
		'name'  => __( 'White', 'tca-color' ),
		'slug'  => 'white',
		'color'	=> $white,
	),
	array(
		'name'  => __( 'Black', 'tca-color' ),
		'slug'  => 'black',
		'color' => $black,
	),
	array(
		'name'  => __( 'Dark Purple', 'tca-color' ),
		'slug'  => 'dark-purple',
		'color' => $darkPurple,
       ),
	array(
		'name'  => __( 'Purple', 'tca-color' ),
		'slug'  => 'purple',
		'color' => $purple,
       ),
	array(
		'name'  => __( 'Steel Blue', 'tca-color' ),
		'slug'  => 'steel-blue',
		'color' => $steelBlue,
    ),
	array(
		'name'  => __( 'Gray', 'tca-color' ),
		'slug'  => 'gray',
		'color' => $gray,
    ),
	array(
		'name'  => __( 'Sky Blue', 'tca-color' ),
		'slug'  => 'sky-blue',
		'color' => $skyBlue,
    ),
	array(
		'name'  => __( 'Green', 'tca-color' ),
		'slug'  => 'green',
		'color' => $green,
    ),
	array(
		'name'  => __( 'Tint', 'tca-color' ),
		'slug'  => 'tint',
		'color' => $tint,
    ),
) );



function editor_setup() {

    add_theme_support( 'editor-styles' );

add_editor_style( 'public/css/editor.css' );

}
add_action( 'after_setup_theme', 'editor_setup' );

/**
 * Fix ACF blocks saved with mode "auto" (hides the preview/edit toolbar toggle).
 */
function tca_fix_acf_block_auto_mode() {
	$script = <<<'JS'
	wp.domReady( () => {
		const { select, dispatch, subscribe } = wp.data;
		let migrated = false;

		const collectBlocks = ( blocks ) => {
			return blocks.flatMap( ( block ) => [
				block,
				...collectBlocks( block.innerBlocks || [] ),
			] );
		};

		const migrateAutoModeBlocks = () => {
			if ( migrated || ! select( 'core/block-editor' ) ) {
				return;
			}

			const blocks = collectBlocks(
				select( 'core/block-editor' ).getBlocks()
			);
			const updates = [];

			blocks.forEach( ( block ) => {
				if (
					block.name &&
					block.name.indexOf( 'tca/' ) === 0 &&
					block.attributes &&
					block.attributes.mode === 'auto'
				) {
					updates.push( {
						clientId: block.clientId,
						attributes: { mode: 'preview' },
					} );
				}
			} );

			if ( ! updates.length ) {
				migrated = true;
				return;
			}

			updates.forEach( ( update ) => {
				dispatch( 'core/block-editor' ).updateBlockAttributes(
					update.clientId,
					update.attributes
				);
			} );

			migrated = true;
		};

		subscribe( migrateAutoModeBlocks );
		migrateAutoModeBlocks();
	} );
JS;

	wp_register_script( 'tca-acf-block-mode-fix', false, array( 'wp-dom-ready', 'wp-data', 'wp-edit-post' ), _S_VERSION, true );
	wp_enqueue_script( 'tca-acf-block-mode-fix' );
	wp_add_inline_script( 'tca-acf-block-mode-fix', $script );
}
add_action( 'enqueue_block_editor_assets', 'tca_fix_acf_block_auto_mode' );


add_action( 'admin_footer', function () {


	?>
    	<script>
	    if (window.acf) {
	        acf.addFilter('color_picker_args', function (args, $field) {

		    args.palettes = [
		        	"#ffffff",
					"#000000",
					"#0C0042",
					"#260D66",
					"#385DFF",
					"#315372",
					"#9ADCF7",
					"#00B852",
					"#E5ECF2",
					"#F1F5F8"
		    ];

			

		    return args;
	        });
	    }
    	</script>
	<?php
} );
function my_mce4_options($init) {

    $custom_colours = '
        "ffffff", "White",
        "000000", "Black",
        "0C0042", "Dark Purple",
        "260D66", "Purple",
        "385DFF", "Steel Blue",
        "00B852", "Green",
		"F1F5F8", "Light Blue"
    ';

    // build colour grid default+custom colors
    $init['textcolor_map'] = '['.$custom_colours.']';

    // change the number of rows in the grid if the number of colors changes
    // 8 swatches per row
    $init['textcolor_rows'] = 1;

    return $init;
}
add_filter('tiny_mce_before_init', 'my_mce4_options');

