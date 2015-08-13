<?php
/**
* Class : WPDU_MRT
* @version 1.0.0
* @package WP Display Users
*/

class WPDU_MRT extends WP_List_Table {
    
	var $wpdu_manage_rule_data;
	var $found_data;
	
	function __construct() {
		
		global $status, $page, $wpdb;
		
		parent::__construct( array(
				'singular'  => 'wpdu-user',    
				'plural'    => 'wpdu-users',  
				'ajax'      => false       
		) );
		
		if( $_GET['page']=='wp-display-users' && !empty($_POST['s']) )
		{
			$query = "SELECT * FROM ".TBL_DU." WHERE wpdu_rule_title LIKE '%".$_POST['s']."%'";
		}
		else
		{
			$query = "SELECT * FROM ".TBL_DU."";
		}
			
		$this->wpdu_manage_rule_data = $wpdb->get_results($query, ARRAY_A);
		add_action( 'admin_head', array( &$this, 'admin_header' ) );            
	}
	
	function admin_header() {
		
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if( 'wp-display-users' != $page )
		return;
		
		echo '<style type="text/css">';
		echo '.wp-list-table .column-wpdu_rule_title  { width: 20%; }';
		echo '.wp-list-table .column-wpdu_shortcode  { width: 20%;}';
		echo '</style>';
	}
	  
	function no_items() {
		
		echo 'No records founds in database.';
	}
		
	function column_default( $item, $column_name ) {
		
		switch( $column_name ) {
			case 'wpdu_rule_title': 
			case 'wpdu_shortcode':
			default:
			return $item[$column_name] ; //Show the whole array for troubleshooting purposes
		}
	}
	  
	function custom_column_value($column_name,$item) {
		
		if($column_name=='post_title ')
		return "<a href='".get_permalink( $item[ 'post_id' ] )."'>".$item[ $column_name ]."</a>";
		elseif($column_name=='user_login')
		return "<a href='".get_author_posts_url($item[ 'user_id' ])."'>".$item[ $column_name ]."</a>";
		else
		return $item[ $column_name ];
	}
	
	function get_sortable_columns() {
		
		$sortable_columns = array(
			'wpdu_rule_title'   => array('wpdu_title',false),
			'wpdu_shortcode'   	=> array('wpdu_shortcode',false)
		);
		return $sortable_columns;
	}
	
	function get_columns() {
		
		$columns = array(
		'cb'        => '<input type="checkbox" />',
		'wpdu_rule_title' => 'Rule Title',
		'wpdu_shortcode'  => 'Shortcode'
		);
		return $columns;
	}
	
	function usort_reorder( $a, $b ) { 
	 
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : '';
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
		$result = strcmp( $a[$orderby], $b[$orderby] );
		return ( $order === 'asc' ) ? $result : -$result;
	}
	
	function column_wpdu_rule_title($item) {
		
		$actions = array(
				'edit'      => sprintf('<a href="?page=%s&tab=manage-rule&action=%s&wpdu_id=%s">Edit</a>',$_REQUEST['page'],'edit',$item['wpdu_id']),
				'delete'    => sprintf('<a href="?page=%s&tab=manage-rule&action=%s&wpdu_id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['wpdu_id']),
			);
		return sprintf('%1$s %2$s', $item['wpdu_rule_title'], $this->row_actions($actions) );
	}
	
	function get_bulk_actions() {
		
	  $actions = array(
		'delete' => 'Delete'
	  );
	  return $actions;
	}
	
	function column_cb($item) {
		
		return sprintf(
			'<input type="checkbox" name="wpdu_id[]" value="%s" />', $item['wpdu_id']
		);
	}
	
	function column_wpdu_shortcode($item) {
		
		return sprintf(
			'[wp_display_user id='.$item["wpdu_id"].']'
		);
	}
	
	function prepare_items() {
		
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		usort( $this->wpdu_manage_rule_data, array( &$this, 'usort_reorder' ) );
	
		$per_page = 10;
		$current_page = $this->get_pagenum();
		$total_items = count( $this->wpdu_manage_rule_data );
		$this->found_data = array_slice( $this->wpdu_manage_rule_data,( ( $current_page-1 )* $per_page ), $per_page );
		$this->set_pagination_args( array(
		'total_items' => $total_items,                  //WE have to calculate the total number of items
		'per_page'    => $per_page                     //WE have to determine how many items to show on a page
		) );
		$this->items = $this->found_data;
	}
}

$con = new wpdu_Database();

if( !empty($_GET['action']) && $_GET['action']=='delete' && !empty($_GET['wpdu_id']) && $_GET['tab']=='manage-rule' ) {

	$id = (int)$_GET['wpdu_id'];
	
	$query="DELETE FROM ".$this->table." WHERE wpdu_id='$id'";
	
	$del = $con->Run_Query($query);
	
	if($del==1)
	$response["success"] = __( 'Selected Record Deleted Successfully.', 'wp-widget-bundle' );	
}

if( !empty($_POST['action']) && $_POST['action'] == 'delete' && !empty($_POST['wpdu_id']) && $_GET['tab']=='manage-rule' ) {

	foreach($_POST['wpdu_id'] as $id)
	{
		$query="DELETE FROM ".$this->table." WHERE wpdu_id='$id'";
		$del = $con->Run_Query($query);				
	}

    $response["success"]= __( 'Selected Record Deleted Successfully.', 'wp-widget-bundle' );
}

if( !empty($_GET['action']) && $_GET['action']=='edit' && !empty($_GET['wpdu_id']) ) {

    $this->load($_GET['wpdu_id']);
    $rule_data = $this;
    $rule_data_array = (array)$rule_data;
    $_POST = $rule_data_array;
	$unserialize_wpdu_user_roles = unserialize(stripslashes($rule_data_array['wpdu_user_roles']));
	
	$_POST['wpdu_user_roles'] = $unserialize_wpdu_user_roles;
	
    include( WPDU_FORMS . '/wpdu-create-user-rule.php');
}
else
{
?>
    <div class="wpdu_contant">
    
        <?php require_once(WPDU_FUNCTION.'wpdu-function.php'); ?> 
        
		<?php
        $wpdu_manage_rule_table = new WPDU_MRT();
        $wpdu_manage_rule_table->prepare_items();
        ?>
        <form method="post">
            <?php
            $wpdu_manage_rule_table->search_box( 'search', 'search_id' );
            $wpdu_manage_rule_table->display();
            ?> 
        </form> 
        
    </div>
<?php
}