<?php

// Keys and Data
$privateKey = openssl_pkey_get_private( 'file://' . __DIR__ . '/private.pem');
$publicKey = openssl_pkey_get_public( 'file://' . __DIR__ . '/public.pem');
$data = file_get_contents( 'file://' . __DIR__ . '/Sample.jpg' );

/**
 * Sign
 */
openssl_sign($data, $signature, $privateKey);
openssl_free_key($privateKey);

file_put_contents( 'file://' . __DIR__ . '/Sample.sig', base64_encode( $signature ) );

/**
 * Verify
 * 0 for fail & 1 for success
 */

$ok = openssl_verify( $data, base64_decode( file_get_contents( 'file://' . __DIR__ . '/Sample.sig' ) ), $publicKey );

var_dump($ok);
