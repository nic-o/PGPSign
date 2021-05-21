<?php
if ( isset( $_REQUEST['pgpkey-is-generated'] )  && wp_verify_nonce( $_REQUEST['pgpkey-is-generated'], 'pgpkey-generate') ) {
	
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
}

?>
<div class="wrap">
	<h1>PGP Sign & Verify</h1>
	<h2>Public / Private Keys:</h2>
	<?php $pgpkey_list_table->display() ?>

	<h2>Generate a new Private / Public key</h2>
	<p>If you create a new pair of private / public key, this is the key that will be used from now to sign the new document. But to verify the previous documents, the plugin will use the corresponding key.<br>
	The key is generated using the <a href="https://www.php.net/manual/en/book.openssl.php" target="_blank">PHP library OpenSSL</a> with the parameters in <a href="<?php menu_page_url('pgpsign-settings-slug', true); ?>">the settings</a></p>

	<form id="pgpkey-generate" method="post" action="">
		<?php
			wp_nonce_field( 'pgpkey-generate', 'pgpkey-is-generated' );
			submit_button('Generate new key');
		?>
	</form>
</div>