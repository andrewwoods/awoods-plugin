<?php
/*
Plugin Name: AWoods Development
Version: 0.1-alpha
Description: A Development plugin by awoods. It a tool to help with WordPress Development and Administroation. It integrates with WP-CLI.
Author: awoods
Author URI: http://andrewwoods.net
Plugin URI: https://github.com/andrewwoods/awoods-development
Text Domain: awoods
Domain Path: /languages
*/

define( 'AWOODS_DIR_PATH', plugin_dir_path( __FILE__ ) ); 
define( 'AWOODS_DIR_URL',  plugin_dir_url( __FILE__ ) ); 


register_activation_hook( __FILE__, array('Awoods_Setup', 'activate') );
register_deactivation_hook( __FILE__, array( 'Awoods_Setup', 'deactivate' ) );

require_once AWOODS_DIR_PATH . "setup.php";

/**
 * AWoods Development assits you with WordPress development 
 *
 * @package awoods
 * @author awoods
 */ 
class Awoods_Setup
{
	/**
	 * Runs with this plugin is activated
	 *
	 * @return void
	 */
	static public function activate() {
		if ( WP_DEBUG ) {
			error_log( 'Activated "awoods"' );
		} 

		spl_autoload_register( array( 'Awoods_Setup', 'autoloader')  );
	} 

	/**
	 * Runs when this plugin is deactivated
	 *
	 * @return void
	 */ 
	static public function deactivate() {
		if ( WP_DEBUG ) {
			error_log( 'Deactivated "awoods"' );
		} 
	} 


	/*
	 * Include this in your primary plugin file
	 *
	 * Create a 'classes' directory in your plugin directory.
	 * To load a class nameed So_Awesome, it should be in classes/class-so-awesome.php
	 */
	static function autoloader( $class_name ) {
		$file_path = self::get_class_path( $class_name );

		if ( file_exists( $file_path ) ) {
			include_once $file_path;
		}
	}

	/**
	 * Determine the path to a file containing the $class 
	 *
	 * @uses sanitize_title_with_dashes
	 * @param string $class_name a necessary parameter
	 * @return string
	 */
	static public function get_class_path( $class_name ) {
		$class_name = sanitize_title_with_dashes( $class_name, '', 'save' );

		if ( false !== strpos($class_name, '_') ) {
			$class_name = str_replace('_', '/', $class_name);
		} 

		$file = $class_name . '.php';
		
		$file_path = AWOODS_DIR_PATH . 'lib/' . $file;

		return $file_path;
	} 

}



