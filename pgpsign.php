<?php
/**
* PGP Sign & Verify
*
* @package           PGPSignVerify
* @author            Nicolas Georget
* @copyright         2021 Integrity Asia
* @license           GPL-2.0-or-later
*
* @wordpress-plugin
* Plugin Name:       PGP Sign & Verify
* Plugin URI:        https://www.integrity-asia.com
* Description:       This plugin sign and verify a document using OpenSSL library.
* Version:           1.0.0
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            Nicolas Georget
* Author URI:        https://keybase.io/ngeorget
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PGPSIGN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PGPSIGN_PLUGIN_URL', plugins_url( 'pgpsign' ) );

require_once( PGPSIGN_PLUGIN_DIR . 'pgpsign-install.php' );
require_once( PGPSIGN_PLUGIN_DIR . 'pgpsign-admin.php' );

// Init the plugin
register_activation_hook( __FILE__, 'pgpsign_install' );
register_activation_hook( __FILE__, 'pgpsign_install_data' );
register_uninstall_hook(__FILE__, 'pgpsign_uninstall');

// Admin page
add_action('admin_menu', 'pgpsign_setup_menu');



/**
* Shortcodes
*/

function test_output () {
	
	$text = 'tete';
	
	return $text;
}

add_shortcode('test_output', 'test_output');


/******************************************************************************
*
* Private Functions
* 
*****************************************************************************/


/**
* Generate UUID
* Source: https://www.uuidgenerator.net/dev-corner/php
*/

function generateUUID ($data = null) {
	// Generate 16 bytes (128 bits) of random data or use the data passed into the function.
	$data = $data ?? random_bytes(16);
	assert(strlen($data) == 16);
	
	// Set version to 0100
	$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
	// Set bits 6-7 to 10
	$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
	
	// Output the 36 character UUID.
	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
* Functions for generate PGP Signature and Verify a signature
*/

function generatePGPKeys () {
	
	$config = get_option('pgpsign_options');
	
	// Create the keypair  
	$res = openssl_pkey_new( $config );  
	
	// Get private key  
	openssl_pkey_export($res, $privatekey );  
	
	// Get public key  
	$publickey = openssl_pkey_get_details( $res );  
	$publickey = $publickey["key"];  
	
	return array (
		'privatekey' => $privatekey,
		'publickey'  => $publickey
	);
}