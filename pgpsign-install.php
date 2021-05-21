<?php

/**
 * Create MySQL Table on _install() and delete table on _uninstall()
 * Source:  https://codex.wordpress.org/Creating_Tables_with_Plugins
 *          https://developer.wordpress.org/plugins/plugin-basics/uninstall-methods/
 */

global $pgpsign_db_version;
$pgpsign_db_version = '1.0';

function pgpsign_install() {
	global $wpdb;
	global $pgpsign_db_version;

	// Table wp_pgpsign who records the signatures
	$table_name = $wpdb->prefix . 'pgpsign';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		uuid varchar(36) NOT NULL,
		created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		referral varchar(255) DEFAULT '' NOT NULL,
		keyid mediumint(9) NOT NULL,
		pgpsign longtext DEFAULT '' NOT NULL,
		remark longtext default NULL,
		PRIMARY KEY (uuid)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	// Table wp_pgpkey that store the public / private key
	$table_name = $wpdb->prefix . 'pgpkey';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		privatekey varchar(4096) DEFAULT '' NOT NULL,
		publickey varchar(4096) DEFAULT '' NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	dbDelta( $sql );

	add_option( 'pgpsign_db_version', $pgpsign_db_version );
}

function pgpsign_uninstall() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'pgpsign';
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query($sql);
	$table_name = $wpdb->prefix . 'pgpkey';
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query($sql);
	delete_option("pgpsign_db_version");
	delete_option('pgpsign_options');
}

function pgpsign_install_data() {

	/**
	 * TODO
	 * 		[x] ~~*Generate a public / private key by default at index #1*~~ [2021-04-24]
	 *      [ ] Sign a file as testing (~/testing/Sample.jpg)
	 *      [x] ~~*add_option() by default*~~ [2021-04-24]
	 */
	
	 // Config in table wp_options
	 $options = array(  
		"digest_alg"          => 'sha512',  
		"private_key_bits"    => 2048,  
		"private_key_type"    => 'OPENSSL_KEYTYPE_RSA',
		"permalink"           => 'pgpsign',
		"api_qrcode_root_url" => "https://chart.googleapis.com/chart?",
	);
	add_option('pgpsign_options',$options);

	// Generate the 1st private / public key
	$keys = generatePGPKeys();
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'pgpkey';

	$wpdb->insert( 
		$table_name, 
		array( 
			'created'     => current_time( 'mysql' ), 
			'privatekey'  => $keys['privatekey'],
            'publickey'   => $keys['publickey']
		) 
	);

	// Sign the first document
	$table_name = $wpdb->prefix . 'pgpkey';
	$keys = $wpdb->get_row( "SELECT * FROM $table_name ORDER BY id DESC LIMIT 1" );
	$data = file_get_contents( 'file://' . PGPSIGN_PLUGIN_DIR . '/testing/Sample.jpg' );
	openssl_sign( $data, $signature, $keys->privatekey );

	$table_name = $wpdb->prefix . 'pgpsign';
	$wpdb->insert( 
		$table_name, 
		array( 
            'uuid'     => generateUUID(),
			'created'  => current_time( 'mysql' ), 
			'referral' => $_SERVER["HTTP_REFERER"],
            'keyid'    => $keys->id,
			'pgpsign'  => base64_encode( $signature ),
            'remark'   => 'This is the first file that was signed. It was done at the installation of the plugin.',
		) 
	);

}

