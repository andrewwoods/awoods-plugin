<?php
/**
 * Commands that help with development and reporting information.
 */
class Awoods_Command extends WP_CLI_Command {



	/**
	 * Prints a summary of the current wordpress site.
	 *
	 *
	 * ## EXAMPLES
	 *
	 *     wp awoods summary
	 *
	 * @when before_wp_load
	 */
	function summary( $args, $assoc_args ) {

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
		$this->get_search_engine_visibility();
	}



	/**
	 * Search Engine Visibility
	 *
	 *
	 * ## EXAMPLES
	 *
	 *     wp awoods site-visible
	 *
	 * @subcommand site-visible
	 *
	 * @when before_wp_load
	 */
	function site_visible( $args, $assoc_args ) {
		$this->get_search_engine_visibility();
	}



	/**
	 * Determine the value of the 'blog_public' option.
	 * It controls if search engines should crawl the site.
	 *
	 * @return void
	 */
	protected function get_search_engine_visibility() {
		$args = array('option', 'get', 'blog_public');
		$assoc_args = array(
			'format' => 'json'
		);

		ob_start();
		WP_CLI::run_command( $args, $assoc_args );
		$json = ob_get_clean();

		$json = json_decode( $json, true );

		if ( $json ) {
			$message = 'Site is public';
		} else {
			$message = 'Discouraged search engines from indexing this site';
		}

		self::heading( 'Search Engine Visibility' );
		WP_CLI\Utils\format_items(
			'table',
			array( array( 'Blog Status' => $message ) ),
			array( 'Blog Status' )
		);

	}



	/**
	 * Prints a list of active plugins
	 *
	 *
	 * ## EXAMPLES
	 *
	 *     wp awoods active-plugins
	 *
	 * @subcommand active-plugins
	 *
	 * @when before_wp_load
	 */
	function active_plugins( $args, $assoc_args ) {
		$this->list_active_plugins();
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
	 * Prints the current theme.
	 *
	 *
	 * ## EXAMPLES
	 *
	 *     wp awoods active-theme
	 *
	 * @subcommand active-theme
	 *
	 * @when before_wp_load
	 */
	function active_theme( $args, $assoc_args ) {
		$this->list_active_theme();
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
	 * Create a theme based on Temperance
	 *
	 * ## OPTIONS
	 *
	 * <slug>
	 * : The slug of the new theme.
	 *
	 * [--activate]
	 * : Activate the newly created theme.
	 *
	 * [--enable-network]
	 * : Enable the newly downloaded theme for the entire network.
	 *
	 * [--theme_name=<title>]
	 * : What to put in the 'Theme Name:' header in style.css
	 *
	 * [--theme_uri=<url>]
	 * : What to put in the 'Theme URI:' header in style.css
	 *
	 * [--author_name=<full-name>]
	 * : What to put in the 'Author:' header in style.css
	 *
	 * [--author_uri=<url>]
	 * : What to put in the 'Author URI:' header in style.css
	 *
	 * ## EXAMPLES
	 *
	 *     wp awoods temperance <slug>
	 *
	 * @when before_wp_load
	 */
	function temperance( $args, $assoc_args ) {
		list( $theme_slug ) = $args;

		// https://github.com/andrewwoods/temperance/archive/master.zip
		// gets redirected to here
		$gh_url='https://codeload.github.com/andrewwoods/temperance/zip/master';

		$theme_path = WP_CONTENT_DIR . "/themes";
		$url        = $gh_url;
		$timeout    = 30;
		$data = wp_parse_args( $assoc_args, array(
			'theme_name'  => ucfirst( $theme_slug ),
			'theme_uri'   => 'http://THEME-URI.example.com',
			'author_name' => 'Firstname Lastname',
			'author_uri'  => 'http://AUTHOR-URI.example.com',
			'activate'    => false,
			'enable-network'=> false,
		) );

		$data['description'] = $data['theme_name'] . ", developed by " . $data['author_name'];
		$data['slug']        = $theme_slug;
		$data['text_domain'] = $theme_slug;
		$data['prefix']      = $theme_slug . '_';


		$new_theme_path = "$theme_path/$theme_slug";

		$temperance_master_path = "$theme_path/temperance-master";

		$force = \WP_CLI\Utils\get_flag_value( $data, 'force' );
		$should_write_file = $this->prompt_if_files_will_be_overwritten( $new_theme_path, $force );

		if ( ! $should_write_file ) {
			WP_CLI::log( 'No files created' );
			die;
		}


		$tmpfname = wp_tempnam( $url . '.zip' );
		$response = wp_remote_post( $url, array(
			'timeout'  => $timeout,
			'stream'   => true,
			'redirection' => 5,
			'filename'    => $tmpfname
		) );

		if ( is_wp_error( $response ) ) {
			WP_CLI::error( $response );
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 != $response_code ) {
			WP_CLI::error( "Couldn't create theme (received $response_code response)." );
		}

		$this->maybe_create_themes_dir();
		$this->init_wp_filesystem();
		$unzip_result = unzip_file( $tmpfname, $theme_path );
		unlink( $tmpfname );
		rename( $temperance_master_path, $new_theme_path );



		if ( true === $unzip_result ) {
			WP_CLI::success( "Created theme '{$data['theme_name']}'." );
		} else {
			WP_CLI::error( "Could not decompress your theme files ('{$tmpfname}') at '{$theme_path}': {$unzip_result->get_error_message()}" );
		}

		WP_CLI::debug( print_r( $data, true) );

		$replacements = array();
		$replacements['temperancetheme']                 = $data['text_domain'];
		$replacements['YOUR_THEME_NAME']                 = $data['theme_name'];
		$replacements['Temperance']                      = $data['theme_name'];
		$replacements['AUTHOR_NAME']                     = $data['author_name'];
		$replacements['http://AUTHOR-URI.example.com/']  = $data['author_uri'];
		$replacements['http://THEME-URI.example.com/']   = $data['theme_uri'];
		$replacements['temperance']                      = $data['slug'];

		WP_CLI::warning('About to Rename Files');
		$this->rename_files( $new_theme_path, 'temperance', $data['slug'] );

		WP_CLI::warning("About to Search and Replace");
		$this->search_replace_text( $new_theme_path, $replacements );

		if ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'activate' ) ) {
			WP_CLI::run_command( array( 'theme', 'activate', $theme_slug ) );
		} else if ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'enable-network' ) ) {
			WP_CLI::run_command( array( 'theme', 'enable', $theme_slug ), array( 'network' => true ) );
		}

	}



	/**
	 * Search/Replace multiple text in files within the folder
	 *
	 * @param string $dir_path
	 * @param array $data
	 * @return void
	 */
	protected function search_replace_text( $dir_path, $data ) {

		$files = $this->get_files( $dir_path );
		foreach ( $files as $filename ) {

			// skip things that aren't files
			if ( in_array( basename( $filename ), array( '.', '..' ) )  ) {
				continue;
			}

			if ( ! is_file( $filename ) ) {
				continue;
			}

			foreach ( $data AS $value => $replacement ) {
				$content = file_get_contents( $filename );

				file_put_contents( $filename, str_replace( $value, $replacement, $content ) );
			}
		}
	}



	/**
	 * Search/Replace multiple text in files within the folder
	 *
	 * @param string $dir_path
	 * @param array $data
	 * @return void
	 */
	protected function rename_files( $dir_path, $old_slug, $new_slug ) {

		$files = $this->get_files( $dir_path );
		foreach ( $files as $file ) {

			// look only for filenames that have the slug that we want to change
			if ( false !== strpos( $file, $old_slug ) ) {
				$new_file = str_replace( $old_slug, $new_slug, $file );


				$success = rename( $file, $new_file );
				if ( ! $success ) {
					WP_CLI::error( "Could not rename $file to $new_file" );
				} else {
					WP_CLI::success('file renamed to ' . $new_file );
				}
			}
		}
	}



	/**
	 * Retrieve a list of files and subdirectories under $path
	 *
	 * @param string $path a directory path
	 * @return array
	 */
	protected function get_files( $path ) {
		$dir_iterator = new RecursiveDirectoryIterator( $path );
		$iterator = new RecursiveIteratorIterator( $dir_iterator, RecursiveIteratorIterator::SELF_FIRST );
		$files = array();
		foreach ($iterator as $file) {
			$files[] = $file;
		}

		return $files;
	}



	/**
	 * Prompt to override existing assets.
	 *
	 * @param string $filename path
	 * @param bool $force an optional value
	 * @return void
	 */
	private function prompt_if_files_will_be_overwritten( $filename, $force ) {
		$should_write_file = true;
		if ( ! file_exists( $filename ) ) {
			return true;
		}

		WP_CLI::warning( 'File already exists' );
		WP_CLI::log( $filename );
		if ( ! $force ) {
			do {
				$answer = cli\prompt(
					'Skip this file, or replace it with scaffolding?',
					$default = false,
					$marker = '[s/r]: '
				);
			} while ( ! in_array( $answer, array( 's', 'r' ) ) );
			$should_write_file = 'r' === $answer;
		}
		$outcome = $should_write_file ? 'Replacing' : 'Skipping';
		WP_CLI::log( $outcome . PHP_EOL );
		return $should_write_file;
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
	 * Create the themes directory if it doesn't already exist
	 */
	protected function maybe_create_themes_dir() {
		$themes_dir = WP_CONTENT_DIR . '/themes';
		if ( ! is_dir( $themes_dir ) ) {
			wp_mkdir_p( $themes_dir );
		}
	}



	/**
	 * Initialize WP Filesystem
	 */
	private function init_wp_filesystem() {
		global $wp_filesystem;
		WP_Filesystem();
		return $wp_filesystem;
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


	/**
	 * @param string $heading Display a formatted heading
	 * @return void
	 */
	static public function heading($heading) {
		$heading = strtoupper( $heading );

		echo "\n";
		echo "\n";
		WP_CLI::line( $heading );
	}

}

WP_CLI::add_command( 'awoods', 'Awoods_Command' );

