<?php

class MantisKanbanPlugin extends MantisPlugin {

    function register() {
        $this->name = 'Mantis Kanban';    # Proper name of plugin
        $this->description = 'Advanced Kanban board view';    # Short description of the plugin
        $this->page = 'config';           # Default plugin page

        $this->version = '1.1';     # Plugin version string
        $this->requires = array(# Plugin dependencies, array of basename => version pairs
            'MantisCore' => '1.2.0', #   Should always depend on an appropriate version of MantisBT
        );

        $this->author = 'Joanna Chlasta, Stefan Moises';         # Author/team name
        $this->contact = 'moises@shoptimax.de';        # Author/team e-mail address
        $this->url = 'https://github.com/smxsm/MantisKanban';            # Support webpage
    }

    function init() {
        spl_autoload_register(array('MantisKanbanPlugin', 'autoload'));

        $t_path = config_get_global('plugin_path') . plugin_get_current() . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR;

        set_include_path(get_include_path() . PATH_SEPARATOR . $t_path);
        
        // register our custom tables
	#$GLOBALS['g_db_table']['mantis_team_user_table']    = '%db_table_prefix%_team_user%db_table_suffix%';
	#$GLOBALS['g_db_table']['mantis_team_table']         = '%db_table_prefix%_team%db_table_suffix%';
        
    }

    public static function autoload($className) {
        if (class_exists('ezcBase')) {
            ezcBase::autoload($className);
        }
    }

    function hooks() {
        $hooks = array(
            'EVENT_MENU_MAIN' => 'main_menu'
        );
        return $hooks;
    }

    function main_menu() {
        return array('<a href="' . plugin_page('kanban_page') . '">' . plugin_lang_get('main_menu_kanban') . '</a>',);
    }

}
