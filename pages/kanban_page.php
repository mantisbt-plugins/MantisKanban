<?php 

/**
 * @package MantisBT
 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright (C) 2002 - 2010  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 */
 /**
  * MantisBT Core API's
  */
require_once( 'core.php' );

require_api( 'compress_api.php' );
require_api( 'filter_api.php' );
require_api( 'last_visited_api.php' );

auth_ensure_user_authenticated();

$t_current_user_id = auth_get_current_user_id();


# Improve performance by caching category data in one pass
category_get_all_rows( helper_get_current_project() );

compress_enable();

# don't index the kanban page
html_robots_noindex();


html_page_top1( plugin_lang_get( 'kanban_link' ) );

/*
	if ( current_user_get_pref( 'refresh_delay' ) > 0 ) {
		html_meta_redirect( 'my_view_page.php', current_user_get_pref( 'refresh_delay' )*60 );
	}
*/

html_page_top2();

print_recently_visited();

$f_page_number		= gpc_get_int( 'page_number', 1 );

$t_per_page = config_get( 'my_view_bug_count' );
$t_bug_count = null;
$t_page_count = null;

$t_boxes = config_get( 'my_view_boxes' );
asort ($t_boxes);
reset ($t_boxes);
#print_r ($t_boxes);

$t_project_id = helper_get_current_project();

?>
<link rel="stylesheet" type="text/css" href="<?php echo plugin_file( 'kanban.css' ); ?>"/>
<div id="kanbanPage">
	<h1>Kanban Board</h1>

<?php 

$columns = array(
	'Backlog' => array('status' => 10),
	'Assigned Backlog' => array('status' => array(50)),
	'In Progress' => array('status' => array(30, 40)),
	'Feedback' => array('status' => 20),
	'Done' => array('status' => array(80)),
);

?>

<table class="hide kanbanTable" border="0" cellspacing="0" cellpadding="0" style="width: <?php echo (count($columns)*250); ?>px">
<tr>
<?php
$t_per_page = -1;

/**
 * requires current_user_api
 */
require_api( 'current_user_api.php' );
/**
 * requires bug_api
 */
require_api( 'bug_api.php' );
/**
 * requires string_api
 */
require_api( 'string_api.php' );
/**
 * requires date_api
 */
require_api( 'date_api.php' );


/**
 * requires icon_api
 */
require_api( 'icon_api.php' );



$t_icon_path = config_get( 'icon_path' );


# Improve performance by caching category data in one pass
if( helper_get_current_project() == 0 ) {
	$t_categories = array();
	foreach( $rows as $t_row ) {
		$t_categories[] = $t_row->category_id;
	}

	category_cache_array_rows( array_unique( $t_categories ) );
}


foreach($columns as $title => $column){
	$t_per_page = -1;
	?><td><?php 
	
	//$rows = filter_get_bug_rows( $f_page_number, $t_per_page, $t_page_count, $t_bug_count, $c_filter[$t_box_title] );
	
	$rows = filter_get_bug_rows( $f_page_number, $t_per_page, $t_page_count, $t_bug_count, array(
		FILTER_PROPERTY_STATUS => $column['status'],
		'_view_type' => 'advanced'
	));
	
	echo '<h2>'. $title .' ('. $t_bug_count .')</h2>';
	
	
	if(!empty($rows)){
		$i = 0;
		foreach($rows as $row){
			$t_bug = $row;
			
			echo '<div class="card'. ($i%2==1 ? ' cardOdd' : '') . ' card'. category_full_name( $t_bug->category_id, false ) .'">';
			
			// print username instead of status
			if(( ON == config_get( 'show_assigned_names' ) ) && ( $t_bug->handler_id > 0 ) && ( access_has_project_level( config_get( 'view_handler_threshold' ), $t_bug->project_id ) ) ) {
				$emailHash = md5( strtolower( trim( user_get_email($t_bug->handler_id) ) ) );
				echo '<div class="owner">';
				echo '<img src="http://www.gravatar.com/avatar/'. $emailHash .'?s=28&d=mm" />'; 
			
				echo prepare_user_name( $t_bug->handler_id );
				echo '</div>';
			}
			
			echo '<div class="header">';
			echo '	<div class="bugLink">#'. string_get_bug_view_link( $t_bug->id ) .'</div>';
			
			
			
			
			$priority = get_enum_element( 'priority', $t_bug->priority );
			
			echo '	<div class="priority priority'. $t_bug->priority .'" title="'. $priority .'">';
			for($j=0; $j<60; $j+=10){
				echo '<span class="dot dot'. ($j/10+1) .'">'. ($j<$t_bug->priority ? '*' : '&nbsp;') .'</span>';
			}
			echo '	</div>';
			echo '</div>';
			
			echo '<div class="summary" style="clear:left;">'. string_display_line_links( $t_bug->summary ) .'</div>';
			
			$t_last_updated = date( config_get( 'normal_date_format' ), $t_bug->last_updated );
			
			echo '<div class="bugTime"><span class="fake"></span>'. $t_last_updated .'</div>';
			
			echo '<div class="info">';
			
			if( !bug_is_readonly( $t_bug->id ) && access_has_bug_level( $t_update_bug_threshold, $t_bug->id ) ) {
				echo '<a href="' . string_get_bug_update_url( $t_bug->id ) . '"><img border="0" src="/plugins/MantisKanban/files/pencil.png' . '" alt="' . lang_get( 'update_bug_button' ) . '" /></a>';
			}
		
			
			// Check for attachments
			$t_attachment_count = 0;
			if(( file_can_view_bug_attachments( $t_bug->id ) ) ) {
				$t_attachment_count = file_bug_attachment_count( $t_bug->id );
			}
			
			if( 0 < $t_attachment_count ) {
				$t_href = string_get_bug_view_url( $t_bug->id ) . '#attachments';
				$t_href_title = sprintf( lang_get( 'view_attachments_for_issue' ), $t_attachment_count, $t_bug->id );
				$t_alt_text = $t_attachment_count . lang_get( 'word_separator' ) . lang_get( 'attachments' );
				echo "<a class=\"attachments\" href=\"$t_href\" title=\"$t_href_title\"><img src=\"/plugins/MantisKanban/files/paper-clip.png\" alt=\"$t_alt_text\" title=\"$t_alt_text\" /></a>";
				
			}
			if( VS_PRIVATE == $t_bug->view_state ) {
				echo '<img src="' . $t_icon_path . 'protected.gif" width="8" height="15" alt="' . lang_get( 'private' ) . '" />';
			}
			
			echo '</div>';
			echo '</div>';
			
			$i++;
		}
	}
	
	?></td><?php
}

?>
</tr>
</table>
</div>

<?php
	html_page_bottom();
