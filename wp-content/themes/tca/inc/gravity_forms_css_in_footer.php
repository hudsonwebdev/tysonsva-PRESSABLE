<?php
function move_gravity_forms_css_to_footer() {
    // Deregister the original stylesheets
    wp_deregister_style( 'gravity_forms_theme_foundation' );
    wp_deregister_style( 'gravity_forms_theme_framework' );
    wp_deregister_style( 'gravity_forms_theme_reset' );

    // Re-register the stylesheets to load in the footer
    wp_enqueue_style( 'gravity_forms_theme_foundation', plugins_url( 'assets/css/dist/gravity-forms-theme-foundation.min.css', __FILE__ ), array(), 'VER_NUMBER', 'all', true );
    wp_enqueue_style( 'gravity_forms_theme_framework', plugins_url( 'assets/css/dist/gravity-forms-theme-framework.min.css', __FILE__ ), array(), 'VER_NUMBER', 'all', true );
    wp_enqueue_style( 'gravity_forms_theme_reset', plugins_url( 'assets/css/dist/gravity-forms-theme-reset.min.css', __FILE__ ), array(), 'VER_NUMBER', 'all', true );
}
//add_action( 'wp_enqueue_scripts', 'move_gravity_forms_css_to_footer', 99 );
