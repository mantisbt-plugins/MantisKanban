<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );
layout_page_header( lang_get( 'plugin_format_title' ) );
layout_page_begin( 'config.php' );
print_manage_menu();
?>

<div class="col-md-12 col-xs-12">
<div class="space-10"></div>
<div class="form-container" > 
<br/>
<form action="<?php echo plugin_page( 'config_edit' ) ?>" method="post">
<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
	<h4 class="widget-title lighter">
		<i class="ace-icon fa fa-text-width"></i>
		<?php echo  lang_get( 'plugin_format_config' )?>
	</h4>
</div>
<div class="widget-body">
<div class="widget-main no-padding">
<div class="table-responsive"> 
<table class="table table-bordered table-condensed table-striped">  

<tr>
<td align="left">
<?php
$link1=plugin_page('group_add');
$link2=plugin_page('group_edit');
print_link_button( $link1, plugin_lang_get( 'add' ) );
print_link_button( $link2, plugin_lang_get( 'edit' ) );

?>
</td>
</tr>
<tr >
<td class="category" width="60%">
<?php echo lang_get( 'show_empty' )?>
</td>
<td class="center" width="20%">
<label><input type="radio" name='show_empty' value="1" <?php echo( ON == plugin_config_get( 'show_empty' ) ) ? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'enabled' )?></label>

<label><input type="radio" name='show_empty' value="0" <?php echo( OFF == plugin_config_get( 'show_empty' ) )? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'disabled' )?></label>
</td>
</tr> 

<tr >
<td class="category" width="60%">
<?php echo lang_get( 'combined' )?>
</td>
<td class="center" width="20%">
<label><input type="radio" name='combined' value="1" <?php echo( ON == plugin_config_get( 'combined' ) ) ? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'enabled' )?></label>

<label><input type="radio" name='combined' value="0" <?php echo( OFF == plugin_config_get( 'combined' ) )? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'disabled' )?></label>
</td>
</tr> 

</table>
</div>
</div>
<div class="widget-toolbox padding-8 clearfix">
	<input type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo lang_get( 'change_configuration' )?>" />
</div>
</div>
</div>
</form>
</div>
</div> 

<?php
layout_page_end();

