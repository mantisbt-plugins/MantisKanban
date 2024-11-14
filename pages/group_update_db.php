<?php
$id = (int) $_REQUEST['group_id'] ?: 0;
$name	= htmlentities($_REQUEST['group_title'],ENT_COMPAT,'UTF-8');
$status	= htmlentities($_REQUEST['group_status'],ENT_COMPAT,'UTF-8');
$order= (int) $_REQUEST['order_id'] ?: 0;
$query = "UPDATE {plugin_MantisKanban_kanbangroups} SET order_id='$order', group_status  = '$status', group_title = '$name' WHERE group_id = $id";
db_query($query);
print_header_redirect( 'plugin.php?page=MantisKanban/group_edit' );
