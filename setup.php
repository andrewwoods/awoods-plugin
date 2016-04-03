<?php
/**
 * Load WP-CLI commands 
 *
 * @package awoods
 * @author awoods
 */ 

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once dirname( __FILE__ ) . '/lib/wpcli/awoods.php';
}

