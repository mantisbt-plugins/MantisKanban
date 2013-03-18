<?php

/**
 * @package MantisBT
 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright (C) 2002 - 2010  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 * 
 * @author smxsm
 * Project mantis-git
 * @version ##@@VERSION@@##
 * 21.02.2013
 * 
 * Taken from xmlhttprequest.php to use our own
 * AJAX function set, defined in kanban_ajax_api.php.
 * */

/**
 * MantisBT Core API's
 */
require_once( 'core.php' );

require_once( 'logging_api.php' );
require_once( 'kanban_ajax_api.php' );

auth_ensure_user_authenticated();

$f_entrypoint = gpc_get_string( 'entrypoint' );

$t_function = 'kanban_ajax_request_' . $f_entrypoint;
if ( function_exists( $t_function ) ) {
       log_event( LOG_AJAX, "Calling {$t_function}..." );
       call_user_func( $t_function );
} else {
       log_event( LOG_AJAX, "Unknown function for entry point = " . $t_function );
       echo 'unknown entry point: ' . $t_function;
}

?>
