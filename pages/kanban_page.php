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
require_api( 'current_user_api.php' );
require_api( 'bug_api.php' );
require_api( 'string_api.php' );
require_api( 'date_api.php' );

auth_ensure_user_authenticated();
$t_current_user_id = auth_get_current_user_id();

# Improve performance by caching category data in one pass
category_get_all_rows( helper_get_current_project() );

compress_enable();

# don't index the kanban page
html_robots_noindex();

layout_page_header( plugin_lang_get( 'kanban_link' ) );
layout_page_begin();

if ( current_user_get_pref( 'refresh_delay' ) > 0 ) {
	html_meta_redirect( 'my_view_page.php', current_user_get_pref( 'refresh_delay' )*60 );
}

$f_page_number		= gpc_get_int( 'page_number', 1 );

$t_per_page = config_get( 'my_view_bug_count' );
$t_bug_count = null;
$t_page_count = null;

$t_boxes = config_get( 'my_view_boxes' );
asort ($t_boxes);
reset ($t_boxes);

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

<table  border="0" cellspacing="0" cellpadding="0" style="width: <?php echo (count($columns)*250); ?>px">
<tr>
<?php
$t_per_page = -1;

# Improve performance by caching category data in one pass
if( helper_get_current_project() == 0 ) {
	$rows = category_get_all_rows( 0 );
	$t_categories = array();
	foreach( $rows as $t_row ) {
		$t_categories[] = $t_row['id'];
	}
	category_cache_array_rows( array_unique( $t_categories ) );
	$all_project_ids = user_get_accessible_projects( $t_current_user_id );
} else {
	 $all_project_ids = array($t_project_id);
}

// get all user set filters
$t_filter = current_user_get_bug_filter();

foreach($all_project_ids as $curr_project_id) {
	?>
	<tr>
        <td class="projectHeader" colspan="<?php echo count($columns);?>">
            <h1><?php echo project_get_name($curr_project_id); ?></h1>
        </td>
    </tr>
	<?php	
	
	foreach($columns as $title => $column){
		$t_per_page = -1;
		?><td><?php 
	
		$filter_array = array(
            'status' => $column['status'],
            //'sort' => $f_sort_by,
            //'dir' => 'DESC',
             '_view_type' => 'advanced',
            // general filters set by user, add more if needed
            //'category' => $t_filter['category'],
            'priority' => $t_filter['priority'],
            'handler_id' => $t_filter['handler_id'],
        );
	    $rows = filter_get_bug_rows( $f_page_number, $t_per_page, $t_page_count, $t_bug_count,
            $filter_array, $curr_project_id
        );
        $t_rowcounts[$title] = count($rows);
	
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
			
				$priority = get_enum_element( 'priority', $t_bug->priority );
				$t_last_updated = date( config_get( 'normal_date_format' ), $t_bug->last_updated );
				$t_update_bug_threshold = config_get( 'update_bug_threshold' );
				echo '<div class="header">';
				echo '	<div class="bugLink">#'. string_get_bug_view_link( $t_bug->id ); 
				echo ' ';
				echo icon_get_status_icon($t_bug->priority);
				echo '</div>';
				echo '</div>';
				echo '<div class="summary" style="clear:left;">'. string_display_line_links( $t_bug->summary ) .'</div>';
				echo '<div class="bugTime"><span class="fake"></span>'. $t_last_updated .'</div>';
				echo '<div class="info">';
				if( !bug_is_readonly( $t_bug->id ) && access_has_bug_level( $t_update_bug_threshold, $t_bug->id ) ) {
					echo '<a href="' . string_get_bug_update_url( $t_bug->id ) . '"><img border="0" src="plugins/MantisKanban/files/pencil.png' . '" alt="' . lang_get( 'update' ) . '" /></a>';
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
					echo "<a class=\"attachments\" href=\"$t_href\" title=\"$t_href_title\"><img src=\"plugins/MantisKanban/files/paper-clip.png\" alt=\"$t_alt_text\" title=\"$t_alt_text\" /></a>";
				}
				if( VS_PRIVATE == $t_bug->view_state ) {
					echo '<img src="fa-lock" width="8" height="15" alt="' . lang_get( 'private' ) . '" />';
				}
			
				echo '</div>';
				echo '</div>';
			
				$i++;
			}
		}
	
		?></td><?php
	}
}
?>
</tr></table></div>
<?php
layout_page_end();