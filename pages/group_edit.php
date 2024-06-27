<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );
layout_page_header( lang_get( 'plugin_format_title' ) );
layout_page_begin( );
print_manage_menu();
$link=plugin_page('config');
?>
<div class="col-md-12 col-xs-12">
<div class="space-10"></div>
<div class="form-container" > 
<br>
<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
	<h4 class="widget-title lighter">
		<i class="ace-icon fa fa-text-width"></i>
		<?php echo plugin_lang_get( 'title' ) . ': ' . lang_get( 'plugin_format_config' )?>
	</h4>
</div>
<div class="widget-body">
<div class="widget-main no-padding">
<div class="table-responsive"> 
<table class="table table-bordered table-condensed table-striped"> 
<tr>
<td class="form-title" colspan="3">
<?php
print_link_button( $link, plugin_lang_get( 'configuration' ) );
?>
</td>
<td></td>
<td colspan="5" class="row-category"><div align="left"><a name="userdata_record"></a>
</div>
</td>
</tr>
<tr class="row-category">
<td><div><b><?php echo plugin_lang_get( 'title' ); ?></b></div></td>
<td><div><b><?php echo plugin_lang_get( 'status' ); ?></b></div></td>
<td><div><b><?php echo plugin_lang_get( 'order' ); ?></b></div></td>
<td></td>
</tr>
<?PHP
# Pull all definition entries 
$query = "SELECT * FROM {plugin_MantisKanban_kanbangroups} ORDER BY order_id";
$result = db_query($query);
while ($row = db_fetch_array($result)) {
	$name = $row['group_title'];
	$status = $row['group_status'];	
	$order = $row['order_id'];
	?>
	<tr>
	<td><div align="left">
	<?PHP echo $name ; ?>
	</div></td>
	<td><div align="left">
	<?PHP echo $status ; ?>
	</div></td>
	<td><div align="left">
	<?PHP echo $order ; ?>
	</div></td>
	<td><div>
	<?php
	$link2 = "plugin.php?page=MantisKanban/group_delete_db.php&delete_id=";
	$link2 .= $row["group_id"];
	print_link_button( $link2, lang_get( 'delete' ) );
	?>
	</div></td>
	</tr>
	<?php
}	 
?>
</table>
</div>
</div>
</div>
</div>
</form>
</div>
</div>
<?php
layout_page_end( );
