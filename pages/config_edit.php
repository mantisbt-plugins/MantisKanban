<?php
// authenticate
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );
// Read results
$f_show_empty			= gpc_get_int( 'show_empty' );
$f_combined				= gpc_get_int( 'combined' );
// update results
plugin_config_set( 'show_empty', $f_show_empty );
plugin_config_set( 'combined', $f_combined );
// redirect
print_header_redirect( "manage_plugin_page.php" );