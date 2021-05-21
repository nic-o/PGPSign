<?php

if ( isset( $_REQUEST['pgpsign-is-setup'] )  && wp_verify_nonce( $_REQUEST['pgpsign-is-setup'], 'pgpsign-settings') ) {
	$config = array(  
		"digest_alg"          => $_POST['pgpsign-option-digest_alg'],  
		"private_key_bits"    => $_POST['pgpsign-option-private_key_bits'],
		"private_key_type"    => $_POST['pgpsign-option-private_key_type'],
		"permalink"           => $_POST['pgpsign-permalink'],
		"api_qrcode_root_url" => $_POST['pgpsign-api-qrcode'],
	);
	update_option('pgpsign_options', $config);
	$notice = array(
		'class'   => 'notice notice-success',
		'message' => '<h3>Settings saved!</h3>' .
					 '<p>All the settings were savec successfully</p>',
	);
} else {
	$config = get_option('pgpsign_options');
}


?>

<div class="wrap">

	<h1>PGP Sign & Verify</h1>
	<?php if ( isset($notice) ) { pgpsign_display_message ( $notice['class'], $notice['message'] ); } ?>
	<form action="<?php menu_page_url('pgpsign-settings-slug', true); ?>" method="post">
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row" colspan="2"><h2>OpenSSL / PGP Settings:</h2></th>
				</tr>
				<tr>
					<th scope="row"><label for="digest_alg">Digest Method</label></th>
					<td>
						<input name="pgpsign-option-digest_alg" type="text" id="pgpsign-option-digest_alg" value="<?php echo $config['digest_alg']; ?>" class="regular-text" required>
						<p><span class="description">See all the digest methods available: <a href="https://www.php.net/manual/en/function.openssl-get-md-methods.php" target="_blank">openssl_get_md_methods()</a>.</span></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="private_key_bits">Key Length</label></th>
					<td>
						<input name="pgpsign-option-private_key_bits" type="number" id="pgpsign-option-private_key_bits" value="<?php echo $config['private_key_bits']; ?>" class="regular-text" required>
						<p><span class="description">Specifies how many bits should be used to generate a private key (by default 2048 bits).</span></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="private_key_bits">Type of Private Key</label></th>
					<td>
						<select for="gpsign-option-private_key_type" name="pgpsign-option-private_key_type" required>
							<option value="OPENSSL_KEYTYPE_DSA" <?php if ($config['private_key_type'] == 'OPENSSL_KEYTYPE_DSA') echo 'selected'; ?>>OPENSSL_KEYTYPE_DSA</option>
							<option value="OPENSSL_KEYTYPE_DH" <?php if ($config['private_key_type'] == 'OPENSSL_KEYTYPE_DH') echo 'selected'; ?>>OPENSSL_KEYTYPE_DH</option>
							<option value="OPENSSL_KEYTYPE_RSA" <?php if ($config['private_key_type'] == 'OPENSSL_KEYTYPE_RSA') echo 'selected'; ?>>OPENSSL_KEYTYPE_RSA</option>
							<option value="OPENSSL_KEYTYPE_EC" <?php if ($config['private_key_type'] == 'OPENSSL_KEYTYPE_EC') echo 'selected'; ?>>OPENSSL_KEYTYPE_EC</option>
						</select>			
						<p><span class="description">Specifies the type of private key to create (by default OPENSSL_KEYTYPE_RSA).</span></p>
					</td>
				</tr>
				<tr>
					<th scope="row" colspan="2"><h2>Front-end:</h2></th>
				</tr>
				<tr>
					<th scope="row"><label for="permalink">Permalink <span class="description">(required)</span></label></th>
					<td>
						<?php echo get_site_url(); ?>/ <input name="pgpsign-permalink" type="text" id="pgpsign-permalink" value="<?php echo $config['permalink']; ?>" class="regular-text" required>
						<p><span class="description">Without the traling slash at the end.</span></p>
					</td>
				</tr>
				<tr>
					<th scope="row" colspan="2"><h2>API:</h2></th>
				</tr>
				<tr>
					<th scope="row"><label for="api_qrcode">QR Code <span class="description">(required)</span></label></th>
					<td>
						<input name="pgpsign-api-qrcode" type="text" id="pgpsign-api-qrcode" value="<?php echo $config['api_qrcode_root_url']; ?>" class="regular-text" required>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
			wp_nonce_field( 'pgpsign-settings', 'pgpsign-is-setup' );
			submit_button('Save');
		?>
	</form>
	
</div>