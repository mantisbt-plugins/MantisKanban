<?php

class MantisKanbanPlugin extends MantisPlugin {
    function register() {
        $this->name = 'Mantis Kanban';    # Proper name of plugin
        $this->description = 'A Kanban board view';    # Short description of the plugin

        $this->version = '2.0.0';     # Plugin version string
        $this->requires = array(    # Plugin dependencies, array of basename => version pairs
            'MantisCore' => '2.0.0',  #   Should always depend on an appropriate version of MantisBT
            );

        $this->author = 'Joanna Chlasta/Cas Nuy';         # Author/team name
        $this->contact = '';        # Author/team e-mail address
        $this->url = '';            # Support webpage
    }
    
	function hooks( ) {
		$hooks = array(
			'EVENT_MENU_MAIN' => 'main_menu'
		);
		return $hooks;
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

	
}
