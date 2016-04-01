<?php

/*
 * Include this in your primary plugin file
 *
 * Create a 'classes' directory in your plugin directory.
 * To load a class nameed So_Awesome, it should be in classes/class-so-awesome.php
 */
function your_plugin_autoloader( $class_name ) {
    $slug = sanitize_title_with_dashes( $class_name, '', 'save' );
    $slug = str_replace('_', '-', $slug);

    $file = 'class-' . $slug . '.php';
    $file_path = plugin_dir_path( __FILE__ ) . 'classes/' . $file;

    if ( file_exists( $file_path ) ) {
        include_once $file_path;
    }
}

spl_autoload_register( 'your_plugin_autoloader' );


