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
<br/>
<form action="<?php echo plugin_page( 'group_add_db' ) ?>"  method="post" >
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
<td class="form-title" colspan="2">
<?php
print_link_button( $link, plugin_lang_get( 'configuration' ) );
?>
</td>
<td></td>
</tr>

</tr>
<tr>
<td>
<?php //echo lang_get( 'plugin_MantisKanban_title' ) . ': ' . lang_get( 'plugin_Userdata_userdata' ) ?>
</td>
<td colspan="3" class="row-category"><div align="left"><a name="kanban_record"></a></div></td>
</tr>
<tr class="row-category">
<td><div><b><?php echo plugin_lang_get( 'title' ); ?></b></div></td>
<td><div><b><?php echo plugin_lang_get( 'status' ); ?></b></div></td>
<td><div><b><?php echo plugin_lang_get( 'order' ); ?></b></div></td>
</tr>

<tr>

<td><div>
<input name="group_title" type="text" size=50 maxlength=50 >
<br><br>
</td>

<td><div>
<input name="group_status" type="text" size=50 maxlength=50 >
</div>

<td><div>
<input name="order_id" type="number" size=2 maxlength=2 value = 99>
</div>

<td><input name="Submit" type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo lang_get( 'submit' ) ?>">
</td>

</tr>
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
