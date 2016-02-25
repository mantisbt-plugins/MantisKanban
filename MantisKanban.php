<?php

class MantisKanbanPlugin extends MantisPlugin {

    function register() {
        $this->name         = 'Mantis Kanban';
        $this->description  = 'Advanced Kanban board view';
        $this->page         = 'config';

        $this->version = '1.2';
        
        $this->requires = array(
            'MantisCore'    => '1.2.0',
            'jQuery'        => '1.6.2',
        );

        $this->author   = 'Joanna Chlasta, Stefan Moises, Joscha Krug';
        $this->contact  = 'moises@shoptimax.de';
        $this->url      = 'https://github.com/mantisbt-plugins/MantisKanban';
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
            'EVENT_MENU_MAIN'           => 'main_menu',
            'EVENT_LAYOUT_RESOURCES'    => 'resources',
        );
        return $hooks;
    }
    /**
     * Adds a new link to the main menu to enter the kanban board
     * @return array new link for the main menu
     */
    function main_menu() {
        return array('<a href="' . plugin_page('kanban_page') . '">' . plugin_lang_get('main_menu_kanban') . '</a>',);
    }
    
    /**
     * Create the resource link to load the jQuery library.
     */
    function resources( $p_event ) {
            return '<script type="text/javascript" src="' . plugin_file( 'kanban.js' ) . '"></script>'.
                   '<script type="text/javascript">var kanbanAjaxUrl = "' . plugin_page('kanban_ajax_request') . '";</script>';
    }

}
