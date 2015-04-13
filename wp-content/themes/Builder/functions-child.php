<?php

// Tell the main theme that a child theme is running. Do not remove this.
$GLOBALS['builder_child_theme_loaded'] = true;

// Load translations
load_theme_textdomain( 'it-l10n-Builder', get_stylesheet_directory() . '/lang' );

// Add Builder 3.0+ support
add_theme_support( 'builder-3.0' );

// Add support for responsive features
add_theme_support( 'builder-responsive' );
