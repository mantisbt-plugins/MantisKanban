<?php

class MantisKanbanPlugin extends MantisPlugin {
	const GRAVATAR_URL = 'https://secure.gravatar.com/';

    function register() {
        $this->name = 'Mantis Kanban';    # Proper name of plugin
        $this->description = 'A Kanban board view';    # Short description of the plugin

        $this->version = '2.3.0';     # Plugin version string
        $this->requires = array(    # Plugin dependencies, array of basename => version pairs
            'MantisCore' => '2.0.0',  #   Should always depend on an appropriate version of MantisBT
            );
        $this->author = 'Joanna Chlasta/Cas Nuy';         # Author/team name
        $this->contact = 'cas@nuy.info';        # Author/team e-mail address
        $this->page = 'config';            # configuration
		$this->url		= 'https://github.com/mantisbt-plugins/MantisKanban/tree/MantisBT2.x';
    }
	
	 	function config() {
		return array(
			'show_empty'		=> OFF,
			'combined'			=> ON,
			'allowed'			=> ON,
			);
	}
	    
	function hooks( ) {
		$hooks = array(
			'EVENT_CORE_HEADERS' => 'csp_headers',
			'EVENT_MENU_MAIN' => 'main_menu'
		);
		return $hooks;
	}

	function csp_headers() {
		http_csp_add( 'img-src', self::GRAVATAR_URL );
	}
    
	
	function main_menu( ) {
		$links = array();
		$links[] = array(
		'title' => plugin_lang_get( 'main_menu_kanban' ),
		'url' => plugin_page("kanban_page.php", true),
		'icon' => 'fa-dashcube'
		);
		return $links;
	}	

	function schema() {
		# version 2.2.1
		$schema[] = array( 'CreateTableSQL', array( plugin_table( 'kanbangroups' ), "
						group_id			I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
						group_title			C (50)  DEFAULT NULL,
						group_status			C (50)  DEFAULT NULL,
						order_id			I		NOTNULL
						" ) );
		return $schema;
	}
	
}