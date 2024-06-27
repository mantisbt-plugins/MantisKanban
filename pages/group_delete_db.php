<?PHP
$reqVar = '_' . $_SERVER['REQUEST_METHOD'];
$form_vars = $$reqVar;
$delete_id = $form_vars['delete_id'] ;
# Deleting definition
# Need to add check on available data
$query = "DELETE FROM {plugin_MantisKanban_kanbangroups} WHERE group_id = $delete_id";        
db_query($query);
print_header_redirect( 'plugin.php?page=MantisKanban/group_edit' );