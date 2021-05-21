<?php
/**********************************************************************
 * Sign a document
 *********************************************************************/
if ( isset( $_REQUEST['pgpsign-doc-is-signed'] )  && wp_verify_nonce( $_REQUEST['pgpsign-doc-is-signed'], 'pgpsign-sign-doc') ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'pgpkey';

	// First we check if there's KeyID called. If not we query the last one
	if ( !empty( $_POST['keyid'] ) ) {
		$keyid = $_POST['keyid'];
		$keys = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $keyid" );
		if ( is_null ( $keys ) ) {
			$notice = array(
				'class'   => 'notice notice-error',
				'message' => 'The keyID #' . $_POST['keyid'] . ' seems to not exist. Please verify the KeyID you use.'
			);
		}
	} else {
		$keys = $wpdb->get_row( "SELECT * FROM $table_name ORDER BY id DESC LIMIT 1" );
	}
	
	// Then we sign the file and insert the reult into the database
	$data = file_get_contents( 'file://' . $_FILES['documentUploaded']['tmp_name'] );
	openssl_sign( $data, $signature, $keys->privatekey );
	// free the key from memory
	openssl_free_key($keys->privatekey);

	$table_name = $wpdb->prefix . 'pgpsign';
	$uuid = generateUUID();
	$remark = ( !empty($_POST['remark']) ) ? $_POST['remark'] : NULL;
	$wpdb->insert( 
		$table_name, 
		array( 
            'uuid'     => $uuid,
			'created'  => current_time( 'mysql' ), 
			'referral' => $_POST['referral'], 
            'keyid'    => $keys->id,
			'pgpsign'  => base64_encode( $signature ),
            'remark'   => $remark,
		) 
	);
	$config = get_option('pgpsign_options');
	$notice = array(
		'class'   => 'notice notice-success',
		'message' => '<p>The document "' . $_FILES['documentUploaded']['name'] . '" was signed successfully using the key #' . $keys->id . '.</p>'.
		             '<p>The document is now identified as <a href="' . get_site_url() . '/' . $config['permalink'] . '/' . $uuid . '" target="_blank">' . $uuid . '</a>.</p>',
	);
}
/**********************************************************************
 * Verify a document
 *********************************************************************/
if ( isset( $_REQUEST['pgpsign-doc-is-verified'] )  && wp_verify_nonce( $_REQUEST['pgpsign-doc-is-verified'], 'pgpsign-verify-doc') ) {
	global $wpdb;

	// We fetch the signature of the document based on its UUID
	$table_name = $wpdb->prefix . 'pgpsign';
	$uuid = $_POST['uuid'];
	if ( strlen( $uuid ) < 12 ) {
		$notice = array(
			'class'   => 'notice notice-error',
			'message' => '<p>The unique ID you provided ' . $uuid . ' is too short. Use at least the last 12 digits.</p>',
		);
	} else {
		$document = $wpdb->get_row( "SELECT * FROM $table_name WHERE uuid LIKE '%$uuid'" );
	}
	if ( empty ( $document ) ) {
		$notice = array(
			'class'   => 'notice notice-warning',
			'message' => '<p>Oups! It seems the document ' . $uuid . ' was never signed.</p>',
		);
	} else {
		// We verify the uploaded file if it's matching with the signature in the database
		// First we get the corresponding key from the database using $document->keyid
		$table_name = $wpdb->prefix . 'pgpkey';
		$keys = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $document->keyid" );

		// Second we verify. 0 for fail & 1 for success
		$data = file_get_contents( 'file://' . $_FILES['documentUploaded']['tmp_name'] );
		$valid = openssl_verify($data, base64_decode( $document->pgpsign ), $keys->publickey);
	}

	if ($valid == 1) {
		$notice = array(
			'class'   => 'notice notice-success',
			'message' => '<h3>Valid!</h3>' .
						 '<p>The document ' . $uuid . ' was signed by us. Here\'s the information:</p>' .
			             '<ul style="list-style:circle;padding-left: 4em;"><li>Signed on ' . date_i18n( 'l d F Y \a\t H:i:s', strtotime( $document->created ) ) . '</li>' .
						 '<li>Referral: <a href="' . $document->referral . '" target="_blank">' . $document->referral . '</a></li>' .
						 '<li>Using the KeyID #' . $document->keyid . '</li>' .
						 '<li>Remark: ' . $document->remark . '</li></ul>',
		);
	} else {
		$notice = array(
			'class'   => 'notice notice-error',
			'message' => '<h3>Invalid!</h3>' .
						 '<p>The document you uploaded has been signed by us on ' . date_i18n( 'l d F Y \a\t H:i:s', strtotime( $document->created ) ) .
						 '<br>But the signatures are not matching. Maybe the file has been modified since.</p>',
		);
	}
}

?>
<div class="wrap">

	<h1>PGP Sign & Verify</h1>
	<?php if ( isset($notice) ) { pgpsign_display_message ( $notice['class'], $notice['message'] ); } ?>
	<h2>Sign a document:</h2>

	<form action="<?php menu_page_url('pgpsign-testing-slug', true); ?>" method="post" enctype="multipart/form-data">
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="referral">Referral <span class="description">(required)</span></label></th>
					<td><input name="referral" type="url" id="referral" placeholder="https://www..." class="regular-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="document">Document <span class="description">(required)</span></label></th>
					<td>
						<input name="documentUploaded" type="file" id="documentID" required>
						<p> <span class="description">The file will be deleted once the signature is generated.</span></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="keyid">KeyID</label></th>
					<td>
						<input name="keyid" type="number" id="keyid" placeholder="#123" value="" class="regular-text">
						<p> <span class="description">if a KeyID is not specified, the last generated public / private key will be used. See the <a href="<?php menu_page_url('pgpsign-key-slug', true); ?>">list of available keys</a>.</span></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="remark">Remark</label></th>
					<td>
						<textarea name="remark" type="text" id="remark" value="" rows="3" class="regular-text"></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
			wp_nonce_field( 'pgpsign-sign-doc', 'pgpsign-doc-is-signed' );
			submit_button('Sign the document');
		?>
	</form>

	<hr />

	<h2>Verify the Authenticity:</h2>

	<form action="<?php menu_page_url('pgpsign-testing-slug', true); ?>" method="post" enctype="multipart/form-data">
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="document">Document <span class="description">(required)</span></label></th>
					<td>
						<input name="documentUploaded" type="file" id="documentID" required>
						<p><span class="description">The file will be deleted once the signature will match with one of the signed document.</span></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="uuid">UUID <span class="description">(required)</span></label></th>
					<td>
						<input name="uuid" type="text" id="uuid" value="" pattern=".{12,}" class="regular-text" required>
						<p><span class="description">At least the last 12 digits.</span></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
			wp_nonce_field( 'pgpsign-verify-doc', 'pgpsign-doc-is-verified' );
			submit_button('Verify the document');
		?>
	</form>
	
</div>
<?php



?>