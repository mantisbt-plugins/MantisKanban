<?php

class MantisKanbanPlugin extends MantisPlugin {
    function register() {
        $this->name = 'Mantis Kanban';    # Proper name of plugin
        $this->description = 'A Kanban board view';    # Short description of the plugin
        $this->page = 'config';           # Default plugin page

        $this->version = '1.0';     # Plugin version string
        $this->requires = array(    # Plugin dependencies, array of basename => version pairs
            'MantisCore' => '1.2.0',  #   Should always depend on an appropriate version of MantisBT
            );

        $this->author = 'Joanna Chlasta';         # Author/team name
        $this->contact = 'joanna@thinksentient.com';        # Author/team e-mail address
        $this->url = '';            # Support webpage
    }
    
	function hooks( ) {
		$hooks = array(
			'EVENT_MENU_MAIN' => 'main_menu'
		);
		return $hooks;
	}
    
	
	function main_menu( ) {
		return array( '<a href="' . plugin_page( 'kanban_page' ) . '">' . plugin_lang_get( 'main_menu_kanban' ) . '</a>', );
	}	

	
}
