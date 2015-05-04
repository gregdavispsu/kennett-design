<?php

add_theme_support( 'builder-3.0' );
add_theme_support( 'builder-responsive', array( 'mobile-width' => '450px' ) );

// Alternate Module Styles
if ( ! function_exists( 'it_builder_loaded' ) ) {
  function it_builder_loaded() {
    builder_register_module_style( 'widget-bar', 'Light Gray', 'builder-module-widget-bar-light-gray' );
  }
}
add_action( 'it_libraries_loaded', 'it_builder_loaded' );

// Disable login modals introduced in WordPress 3.6
remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );
