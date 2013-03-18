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
 * AJAX functions, have to be named with kanban_ajax_request_*
 * and should be called via pages/kanban_ajax_request.php.
 * 
 * The initial call has to be *without* "kanban_ajax_request_", e.g.
 * "entrypoint=bug_update_status"
 * 
 * @see kanban_api.php
 * */
require_once( 'core.php' );

require_once( 'bug_api.php' );
require_once( 'bugnote_api.php' );
require_once( 'custom_field_api.php' );

/**
 * updates the status field
 * @return bool 
 * @access public
 * @see bug_update.php
 */
function kanban_ajax_request_bug_update_status() {

    $p_bug_id = gpc_get_int('id');
    $p_new_status = gpc_get_int('new_status');
    $t_project_id = gpc_get_int('project_id');

    $c_bug_id = (int) $p_bug_id;
    $f_new_status = (int) $p_new_status;

    $t_bug_data = bug_get($c_bug_id, true);
    $f_update_mode = gpc_get_bool('update_mode', FALSE); # set if called from generic update page
    if (!(
            ( access_has_bug_level(access_get_status_threshold($f_new_status, $t_project_id), $c_bug_id) ) ||
            ( access_has_bug_level(config_get('update_bug_threshold'), $c_bug_id) ) ||
            ( ( bug_get_field($c_bug_id, 'reporter_id') == auth_get_current_user_id() ) &&
            ( ( ON == config_get('allow_reporter_reopen') ) ||
            ( ON == config_get('allow_reporter_close') ) ) )
            )) {
        access_denied();
    }
    # extract current extended information
    $t_old_bug_status = $t_bug_data->status;
    log_event(LOG_AJAX, "Old bug status {$t_old_bug_status} - trying update to new status {$f_new_status}...");

    $t_bug_data->reporter_id = gpc_get_int('reporter_id', $t_bug_data->reporter_id);
    $t_bug_data->handler_id = gpc_get_int('handler_id', $t_bug_data->handler_id);
    $t_bug_data->duplicate_id = gpc_get_int('duplicate_id', $t_bug_data->duplicate_id);
    $t_bug_data->priority = gpc_get_int('priority', $t_bug_data->priority);
    $t_bug_data->severity = gpc_get_int('severity', $t_bug_data->severity);
    $t_bug_data->reproducibility = gpc_get_int('reproducibility', $t_bug_data->reproducibility);
    $t_bug_data->status = gpc_get_int('new_status', $t_bug_data->status);
    $t_bug_data->resolution = gpc_get_int('resolution', $t_bug_data->resolution);
    $t_bug_data->projection = gpc_get_int('projection', $t_bug_data->projection);
    $t_bug_data->category_id = gpc_get_int('category_id', $t_bug_data->category_id);
    $t_bug_data->eta = gpc_get_int('eta', $t_bug_data->eta);
    $t_bug_data->os = gpc_get_string('os', $t_bug_data->os);
    $t_bug_data->os_build = gpc_get_string('os_build', $t_bug_data->os_build);
    $t_bug_data->platform = gpc_get_string('platform', $t_bug_data->platform);
    $t_bug_data->version = gpc_get_string('version', $t_bug_data->version);
    $t_bug_data->build = gpc_get_string('build', $t_bug_data->build);
    $t_bug_data->fixed_in_version = gpc_get_string('fixed_in_version', $t_bug_data->fixed_in_version);
    $t_bug_data->view_state = gpc_get_int('view_state', $t_bug_data->view_state);
    $t_bug_data->summary = gpc_get_string('summary', $t_bug_data->summary);
    $t_due_date = gpc_get_string('due_date', null);

    if (access_has_project_level(config_get('roadmap_update_threshold'), $t_bug_data->project_id)) {
        $t_bug_data->target_version = gpc_get_string('target_version', $t_bug_data->target_version);
    }

    if ($t_due_date !== null) {
        if (is_blank($t_due_date)) {
            $t_bug_data->due_date = 1;
        } else {
            $t_bug_data->due_date = strtotime($t_due_date);
        }
    }

    $t_bug_data->description = gpc_get_string('description', $t_bug_data->description);
    $t_bug_data->steps_to_reproduce = gpc_get_string('steps_to_reproduce', $t_bug_data->steps_to_reproduce);
    $t_bug_data->additional_information = gpc_get_string('additional_information', $t_bug_data->additional_information);

    $f_private = gpc_get_bool('private');
    $f_bugnote_text = gpc_get_string('bugnote_text', '');
    $f_time_tracking = gpc_get_string('time_tracking', '0:00');
    $f_close_now = gpc_get_string('close_now', false);

    # Handle auto-assigning
    if (( config_get('bug_submit_status') == $t_bug_data->status ) && ( $t_bug_data->status == $t_old_bug_status ) && ( 0 != $t_bug_data->handler_id ) && ( ON == config_get('auto_set_status_to_assigned') )) {
        $t_bug_data->status = config_get('bug_assigned_status');
    }

    helper_call_custom_function('issue_update_validate', array($c_bug_id, $t_bug_data, $f_bugnote_text));

    $t_resolved = config_get('bug_resolved_status_threshold');
    $t_closed = config_get('bug_closed_status_threshold');

    $t_custom_status_label = "update"; # default info to check
    if ($t_bug_data->status == $t_resolved) {
        $t_custom_status_label = "resolved";
    }
    if ($t_bug_data->status == $t_closed) {
        $t_custom_status_label = "closed";
    }

    $t_related_custom_field_ids = custom_field_get_linked_ids($t_bug_data->project_id);
    foreach ($t_related_custom_field_ids as $t_id) {
        $t_def = custom_field_get_definition($t_id);

        # Only update the field if it would have been display for editing
        if (!( (!$f_update_mode && $t_def['require_' . $t_custom_status_label] ) ||
                (!$f_update_mode && $t_def['display_' . $t_custom_status_label] && in_array($t_custom_status_label, array("resolved", "closed")) ) ||
                ( $f_update_mode && $t_def['display_update'] ) ||
                ( $f_update_mode && $t_def['require_update'] ) )) {
            continue;
        }

        # Do not set custom field value if user has no write access.
        if (!custom_field_has_write_access($t_id, $c_bug_id)) {
            continue;
        }

        if ($t_def['require_' . $t_custom_status_label] && !gpc_isset_custom_field($t_id, $t_def['type'])) {
            error_parameters(lang_get_defaulted(custom_field_get_field($t_id, 'name')));
            trigger_error(ERROR_EMPTY_FIELD, ERROR);
        }

        # Only update the field if it is posted, 
        #	or if it is empty, and the current value isn't the default 
        if (!gpc_isset_custom_field($t_id, $t_def['type']) &&
                ( custom_field_get_value($t_id, $c_bug_id) == $t_def['default_value'] )) {
            continue;
        }

        if (!custom_field_set_value($t_id, $c_bug_id, gpc_get_custom_field("custom_field_$t_id", $t_def['type'], NULL))) {
            error_parameters(lang_get_defaulted(custom_field_get_field($t_id, 'name')));
            log_event(LOG_AJAX, "Error setting new status: " . ERROR_CUSTOM_FIELD_INVALID_VALUE . "\nBugdata: " . print_r($t_bug_data, true) . " Line: " . __LINE__);
            trigger_error(ERROR_CUSTOM_FIELD_INVALID_VALUE, ERROR);
        }
    }

    $t_notify = true;
    $t_bug_note_set = false;
    if (( $t_old_bug_status != $t_bug_data->status ) && ( FALSE == $f_update_mode )) {
        # handle status transitions that come from pages other than bug_*update_page.php
        # this does the minimum to act on the bug and sends a specific message
        if ($t_bug_data->status >= $t_resolved && $t_bug_data->status < $t_closed && $t_old_bug_status < $t_resolved) {
            # bug_resolve updates the status, fixed_in_version, resolution, handler_id and bugnote and sends message
            bug_resolve($c_bug_id, $t_bug_data->resolution, $t_bug_data->fixed_in_version, $f_bugnote_text, $t_bug_data->duplicate_id, $t_bug_data->handler_id, $f_private, $f_time_tracking);
            $t_notify = false;
            $t_bug_note_set = true;

            if ($f_close_now) {
                bug_set_field($c_bug_id, 'status', $t_closed);
            }

            // update bug data with fields that may be updated inside bug_resolve(), otherwise changes will be overwritten
            // in bug_update() call below.
            $t_bug_data->handler_id = bug_get_field($c_bug_id, 'handler_id');
            $t_bug_data->status = bug_get_field($c_bug_id, 'status');
        } else if ($t_bug_data->status >= $t_closed && $t_old_bug_status < $t_closed) {
            # bug_close updates the status and bugnote and sends message
            bug_close($c_bug_id, $f_bugnote_text, $f_private, $f_time_tracking);
            $t_notify = false;
            $t_bug_note_set = true;
        } else if ($t_bug_data->status == config_get('bug_reopen_status') && $t_old_bug_status >= $t_resolved) {
            bug_set_field($c_bug_id, 'handler_id', $t_bug_data->handler_id); # fix: update handler_id before calling bug_reopen
            # bug_reopen updates the status and bugnote and sends message
            bug_reopen($c_bug_id, $f_bugnote_text, $f_time_tracking, $f_private);
            $t_notify = false;
            $t_bug_note_set = true;

            // update bug data with fields that may be updated inside bug_resolve(), otherwise changes will be overwritten
            // in bug_update() call below.
            $t_bug_data->status = bug_get_field($c_bug_id, 'status');
            $t_bug_data->resolution = bug_get_field($c_bug_id, 'resolution');
        }
    }

    # Plugin support
    $t_new_bug_data = event_signal('EVENT_UPDATE_BUG', $t_bug_data, $c_bug_id);
    if (!is_null($t_new_bug_data)) {
        $t_bug_data = $t_new_bug_data;
    }

    # Add a bugnote if there is one
    if (false == $t_bug_note_set) {
        bugnote_add($c_bug_id, $f_bugnote_text, $f_time_tracking, $f_private, 0, '', NULL, FALSE);
    }

    # Update the bug entry, notify if we haven't done so already
    $t_bug_data->update(true, ( false == $t_notify));

    helper_call_custom_function('issue_update_notify', array($c_bug_id));

    return true;
}

?>
