<?php

class MantisKanbanPlugin extends MantisPlugin {
    function register() {
        $this->name = plugin_lang_get( 'kanban_name' );    
        $this->description = plugin_lang_get( 'kanban_desc' );    

        $this->version = '2.2.1';     
        $this->requires = array(    
            'MantisCore' => '2.0.0', 
            );

        $this->author = 'Joanna Chlasta/Cas Nuy';         
        $this->contact = '';        
        $this->page = 'config';           
    }
 	function config() {
		return array(
			'show_empty'		=> ON,
			'combined'			=> ON,
			);
	}
	
	function hooks( ) {
		$hooks = array(
			'EVENT_MENU_MAIN' => 'main_menu'
		);
		return $hooks;	}
    
	
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
		# version 1.00
		$schema[] = array( 'CreateTableSQL', array( plugin_table( 'kanbangroups' ), "
						group_id			I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
						group_title			C (50)  DEFAULT NULL,
						group_status			C (50)  DEFAULT NULL,
						order_id			I		NOTNULL
						" ) );
		return $schema;
	}
}
