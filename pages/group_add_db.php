<?PHP

$name	= htmlentities($_REQUEST['group_title'],ENT_COMPAT,'UTF-8');
$status	= htmlentities($_REQUEST['group_status'],ENT_COMPAT,'UTF-8');
$order= $_REQUEST['order_id'];
$query	= "INSERT INTO {plugin_MantisKanban_kanbangroups} ( group_title, group_status , order_id) VALUES ( '$name','$status', '$order' )";
db_query($query);
print_header_redirect( 'plugin.php?page=MantisKanban/config' );
