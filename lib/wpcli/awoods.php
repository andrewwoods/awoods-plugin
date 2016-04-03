<?php
/**
 * Commands that help with development and reporting information.
 */
class Awoods_Command extends WP_CLI_Command {

	/**
	 * Prints a greeting.
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : The name of the person to greet.
	 *
	 * [--type=<type>]
	 * : Whether or not to greet the person with success or error.
	 * ---
	 * default: success
	 * options:
	 *   - success
	 *   - error
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp awoods hello Newman
	 *
	 * @when before_wp_load
	 */
	function hello( $args, $assoc_args ) {
		list( $name ) = $args;

		// Print the message with type
		WP_CLI::success( "Hello, $name!" );
	}


	/**
	 * Prints a summary of the wordpress setup.
	 *
	 *
	 * ## EXAMPLES
	 *
	 *     wp awoods audit Newman
	 *
	 * @when before_wp_load
	 */
	function audit( $args, $assoc_args ) {

		$this->current_wordpress_version();

		$response = WP_CLI::launch_self( 'option get', array( 'home' ), array( 'format' => 'json' ), false, true );
		$opts = array();
		$row = array();
		$row['home'] = json_decode( $response->stdout );
		$opts[] = $row;
			
		WP_CLI\Utils\format_items( 'table', $opts, array( 'home' ) );

		$this->list_active_theme();
		$this->list_active_plugins();
		$this->list_menus();
		$this->list_users();
		$this->list_roles();
		$this->current_wpcli_version();
	}

	/**
	 * Display a list of the active plugins
	 *
	 * @return void
	 */
	protected function list_active_plugins() {
		$args = array('plugin', 'list');
		$assoc_args = array(
			'status' => 'active'
		);

		self::heading( 'Active Plugins' );
		WP_CLI::run_command( $args, $assoc_args );
	} 


	/**
	 * Display a liist of the active theme
	 *
	 * @return void
	 */
	protected function list_active_theme() {
		$args = array('theme', 'list');
		$assoc_args = array(
			'status' => 'active'
		);

		self::heading( "Active Theme" );
		WP_CLI::run_command( $args, $assoc_args );
	} 


	/**
	 * Display a list of all users
	 *
	 * @return void
	 */
	protected function list_users() {
		$args = array('user', 'list');
		$assoc_args = array(
			'fields' => 'ID,user_login,display_name,user_email,roles'
		);

		self::heading( "Current Users" );
		WP_CLI::run_command( $args, $assoc_args );
	} 



	/**
	 * Display a list of the menus
	 *
	 * @return void
	 */
	protected function list_menus() {
		$args = array('menu', 'list');
		$assoc_args = array();

		self::heading( "Menus" );
		WP_CLI::run_command( $args, $assoc_args );
	} 





	/**
	 * Display the current WordPress version
	 *
	 * @return void
	 */
	protected function current_wordpress_version() {
		$args = array('core', 'version');
		$assoc_args = array();

		self::heading( "WordPress Version: " );
		WP_CLI::run_command( $args, $assoc_args );
		echo "\n";
	} 


	/**
	 * Display the current WP-CLI information
	 *
	 * @return void
	 */
	protected function current_wpcli_version() {
		$args = array('cli', 'info');
		$assoc_args = array();

		self::heading( 'WP-CLI information' );
		WP_CLI::run_command( $args, $assoc_args );
		echo "\n";
	} 


	/**
	 * Display the list of roles
	 *
	 * @return void
	 */
	protected function list_roles() {
		$args = array('role', 'list');
		$assoc_args = array();

		self::heading( 'Current Roles' );
		WP_CLI::run_command( $args, $assoc_args );
		echo "\n";
	} 


	static public function heading($heading) {
		$heading = strtoupper( $heading );

		echo "\n";
		echo "\n";
		WP_CLI::line( $heading );
	} 

}

WP_CLI::add_command( 'awoods', 'Awoods_Command' );

