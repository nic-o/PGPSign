<?php

/**
 * https://codex.wordpress.org/Administration_Menus
 */

require_once( PGPSIGN_PLUGIN_DIR . 'includes/admin/class-pgpsign-admin.php' );
require_once( PGPSIGN_PLUGIN_DIR . 'includes/admin/class-pgpkey-admin.php' );

// https://developer.wordpress.org/reference/functions/add_menu_page/
function pgpsign_setup_menu () {
    add_menu_page( 'PGP Sign & Verify', 'PGPSign', 'manage_options', 'pgpsign-slug', NULL, 'dashicons-privacy' );
    add_submenu_page( 'pgpsign-slug', 'Documents signed by PGPSign', 'Signed Documents', 'manage_options', 'pgpsign-slug', 'pgpsign_admin_documents');
    add_submenu_page( 'pgpsign-slug', 'PGPSign Public & Private Keys', 'PGP Keys', 'manage_options', 'pgpsign-key-slug', 'pgpsign_admin_keys');
    add_submenu_page( 'pgpsign-slug', 'Sign & Verify', 'Sign & Verify', 'manage_options', 'pgpsign-testing-slug', 'pgpsign_admin_testing');
    add_submenu_page( 'pgpsign-slug', 'PGPSign Settings', 'Settings', 'manage_options', 'pgpsign-settings-slug', 'pgpsign_admin_settings');
}
 
function pgpsign_admin_documents () {
    // Create an instance of our package class.
	$pgpsign_list_table = new PGPSign_List_Table();

    // Fetch, prepare, sort, and filter our data.
	$pgpsign_list_table->prepare_items();

    // Include the view markup.
	include PGPSIGN_PLUGIN_DIR . 'includes/admin/view-pgpsign-admin.php';

}

function pgpsign_admin_keys () {
    // Create an instance of our package class.
	$pgpkey_list_table = new PGPKey_List_Table();

    // Fetch, prepare, sort, and filter our data.
	$pgpkey_list_table->prepare_items();

    // Include the view markup.
	include PGPSIGN_PLUGIN_DIR . 'includes/admin/view-pgpkey-admin.php';
}

function pgpsign_admin_testing () {

    // Include the view markup.
	include PGPSIGN_PLUGIN_DIR . 'includes/admin/view-pgptesting-admin.php';
}

function pgpsign_admin_settings () {

    // Include the view markup.
	include PGPSIGN_PLUGIN_DIR . 'includes/admin/view-pgpsettings-admin.php';
}

function pgpsign_display_message ( $class, $message) {
    echo '<div class="' . $class . '"><p>' . $message . '</p></div>';
}