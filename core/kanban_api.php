<?php

/**
 * @package MantisBT
 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright (C) 2002 - 2010  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 * 
 * @author smxsm
 * Project mantis-smx
 * @version ##@@VERSION@@##
 * 20.02.2013
 * 
 * Helper functions for Kanban plugin
 * */

/**
 * Utility function for getting status text
 * @param type $status_id
 * @return type
 */
function kanban_get_status_text($status_id) {
    return MantisEnum::getLabel(lang_get('status_enum_string'), $status_id);
}

/**
 * Print Change Status to: AJAXified button
 * This code is similar to button_bug_change_status except that the 
 * button is AJAXified.
 * Uses projax.php
 *
 * @param int $p_bug_id
 * @param int $t_project_id
 * @param int $t_user_id
 * @return null
 */
function kanban_ajax_button_bug_change_status($p_bug_id, $t_project_id, $t_user_id) {
    global $g_projax;
    $t_bug_project_id = bug_get_field($p_bug_id, 'project_id');
    $t_bug_current_state = bug_get_field($p_bug_id, 'status');
    $t_current_access = access_get_project_level($t_bug_project_id);

    $t_enum_list = get_status_option_list($t_current_access, $t_bug_current_state, false, ( bug_get_field($p_bug_id, 'reporter_id') == auth_get_current_user_id() && ( ON == config_get('allow_reporter_close') )), $t_bug_project_id);

    if (count($t_enum_list) > 0) {

        # resort the list into ascending order after noting the key from the first element (the default)
        $t_default_arr = each($t_enum_list);
        $t_default = $t_default_arr['key'];
        ksort($t_enum_list);
        reset($t_enum_list);

        echo "<div id=\"ajax_statuschange\"><form method=\"post\" id=\"ajax_status_form\" action=\"xmlhttprequest.php\">";
        # CSRF protection not required here - form does not result in modifications
        echo "<input type=\"hidden\" name=\"project_id\" id=\"project_id\" value=\"$t_project_id\" />";
        echo "<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"$t_user_id\" />";
        echo "<input type=\"hidden\" name=\"entrypoint\" id=\"entrypoint\" value=\"bug_update_status\" />";

        $t_button_text = lang_get('bug_status_to_button');
        // AJAX button options
        $options = array(
            'url' => plugin_page('kanban_ajax_request'), 
            'with' => true, 
            'confirm' => lang_get('confirm_change_status'), 
            'success' => 'location.reload()', 
            'failure' => 'alert("Error: " ' + request . status + ')'
        );
        echo $g_projax->submit_to_remote('ajax_status', $t_button_text, $options);

        echo " <select name=\"new_status\">";
        # space at beginning of line is important
        foreach ($t_enum_list as $key => $val) {
            echo "<option value=\"$key\" ";
            check_selected($key, $t_default);
            echo ">$val</option>";
        }
        echo '</select>';

        $t_bug_id = string_attribute($p_bug_id);
        echo "<input type=\"hidden\" name=\"id\" value=\"$t_bug_id\" />\n";

        echo "</form></div>\n";
    }
}

?>
