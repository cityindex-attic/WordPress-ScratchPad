<?php


/*Add  Option In Setting */

add_action('admin_menu', 'tooldemo_menu');

	function tooldemo_menu() {
  			add_options_page( 'Tools Demo','Tools Demo', 'update_plugins', 'tooldemo', 'tooldemo_options');
		}

/*Admin url*/

$tooldemo_adminurl = admin_url().'options-general.php?page=tooldemo';		
/*Tools Demo Options*/

	function tooldemo_options() {
		if($_POST) {
			tooldemo_save($_POST);
		}

		$action = isset($_GET['action']) ? $_GET['action'] : false;
		switch($action){
		case 'new' :
			tooldemo_edit();
			break;
			case 'new':
			tooldemo_edit();
			break;
		case 'edit':
			$tool_id = intval($_GET['tool_id']);
			tooldemo_edit($tool_id);
			break;
		case 'delete':
			$tool_id = intval($_GET['tool_id']);
			check_admin_referer('tooldemo_delete'.$tool_id);
			tooldemo_delete($tool_id);
			
			// now display summary page
			tooldemo_list();
			break;
			default:
			tooldemo_list();
		}
	}


/* Tools Data Listing function*/

	function tooldemo_list() {
		global $wpdb, $tooldemo_adminurl;
		$table_name = $wpdb->prefix.'tooldemo';
		$pageURL = $tooldemo_adminurl;
		$where = '';
		
		// Display data Query
		
		 $rows = $wpdb->get_results("SELECT * FROM $table_name $where ORDER BY tool_id");
?>
            
            <div class="wrap">
            	<div class="tablenav">
  					<div class="alignleft actions">
    					<input type="submit" class="button-secondary action" id="tooldemo_add" name="tooldemo_add" value="Add New" onclick="location.href='options-general.php?page=tooldemo&action=new'"/>
  					</div>
				</div>
            <table class="widefat">
            <thead>
              <tr>
                <th>Tool ID</th>
                <th>Title</th>
                <th>URL</th>
                <th>Option</th>
              </tr>
            </thead>
            <tbody>
  <?php 		
                    foreach($rows as $row) {
                        echo '<tr>
                        <td>'.$row->tool_id.'</td>
                        <td>'.$row->title.'</td>
                        <td>'.$row->url.'</td>
                        <td><a href="'.$pageURL.'&action=edit&tool_id='.$row->tool_id.'">Edit</a><br/>';
                        $del_link = wp_nonce_url($pageURL.$del_paged.'&action=delete&tool_id='.$row->tool_id, 'tooldemo_delete' . $row->tool_id);
                        echo '<a onclick="if ( confirm(\'You are about to delete Tool #'.$row->title.'\n Cancel to stop, OK to delete.\') ) { return true;}return false;" href="'.$del_link.'" title="Delete this post" class="submitdelete">Delete</a>';
                        echo  '</td></tr>';		
                    }
                    	echo '</tbody></table>';
                		echo '</div>';
         }
		  
/* Tools Data Delete function*/
		function tooldemo_delete($tool_id){
				global $wpdb;
		 		$table_name = $wpdb->prefix .'tooldemo';
		 		$tool_id = intval($tool_id);
		 		$sql = "DELETE FROM $table_name WHERE tool_id = $tool_id";
		 		$wpdb->query($sql);
			}
			
/* Tools Data Save */

		function tooldemo_save($data) {
			global $wpdb;
			$table_name = $wpdb->prefix .'tooldemo';
			$tool_id = intval($data['tool_id']);
			check_admin_referer('tooldemo_edit'.$tool_id);
			$sqldata = array();
			// single record insert/update
			
			$sqldata['title'] = trim(stripslashes($data['tool_title']));
			$sqldata['url'] .= trim(stripslashes($data['tool_url']));
			
			if($tool_id)
					$wpdb->update($table_name, $sqldata, array('tool_id'=>$tool_id));
			else
					$wpdb->insert($table_name, $sqldata);
					wp_redirect('options-general.php?page=tooldemo');
			}
	                        
/*Edit function*/

	function tooldemo_edit($tool_id=0) {
	
		echo '<div class="wrap">';
		$title = '- Add New';
		
		if($tool_id) {
		$title = '- Edit';
		global $wpdb;
		$table_name = $wpdb->prefix . 'tooldemo';
		$sql = "SELECT * from $table_name where tool_id=$tool_id";
		$row = $wpdb->get_row($sql);
		}
	if($tool_id && !$row) {
		echo '<h3>The requested entry was not found.</h3>';
	} else {
	// display the add/edit form 
	global $tooldemo_adminurl;
?>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
  	<script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
  		<script>
   			$(document).ready(function(){
    		$("#commentForm").validate();
  		});
  		</script>

<?php screen_icon();
echo '<h2>Tools Demo:</h2>';
	echo '<form method="post" id="commentForm" action="'.$tooldemo_adminurl.'">
		'.wp_nonce_field('tooldemo_edit'.$tool_id).'
		<input type="hidden" tool_id="tool_id" name="tool_id" value="'.$tool_id.'">
		<div class="lab1">Tool Title:</div>
				<input name="tool_title" id="lab1_input" class="required" value="'.apply_filters('format_to_edit',$row->title).'"/><br/>
				<div class="lab1">Tool Url:</div>
				<input name="tool_url" id="lab1_input" class="required url" value="'.apply_filters('format_to_edit',$row->url).'"/>
				</div>';
		
		echo '<div class="submit">
			<input class="button-primary" type="submit" name="tooldemo_Save" value="Save Changes" />
			</div>
			</form>';
	}
  echo '</div>';
}
?>