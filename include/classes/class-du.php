<?php
/**
* Class : WPDU_DU
* @version 1.0.0
* @package WP Display Users
*/

if ( !class_exists( 'WPDU_DU' ) ) {

	class WPDU_DU extends WPDU_Plugin_Base {

		public $wpdu_id = '';
		public $wpdu_rule_title;	
		public $wpdu_user_roles;		
		public $wpdu_user_incexe;
		public $wpdu_author_id;
		public $wpdu_user_name;
		public $wpdu_user_email;
		public $wpdu_user_description;
		public $wpdu_user_website;
		public $wpdu_user_limit;
		public $wpdu_order_by;
		public $wpdu_order;		
			
		public $valiations = array(	
			'wpdu_rule_title' => array('req' => 'Please enter here title.')	
		);
		
		function WPDU_DU() {

			$this->table = TBL_DU;
			$this->unique = 'wpdu_id';
		}
		
		function load($load_id) {

			$object = $this->Get($this->table,array(array("wpdu_id","=",$load_id)));
			
			if(isset($object)) {

				$this->fill($object[0]);	
			}
		}
		
		function fill($row) {

			$all_properties = get_object_vars($this);
			
			if(is_array($all_properties)) {

				foreach($all_properties as $name => $value) {
									
					if( !empty($row->{$name}) ) {
					
						$this->setVal($name, $row->{$name});
					}		
				}
			}
		}
		
		function Save() {

			if(is_array($this->errors) and !empty($this->errors))
			return false;
			
			$connection = WPDU_Database::Connect();
			$rows = 0;
			
			if ( $this->{$this->unique} != '' ){
				$this->query = $connection->prepare("SELECT $this->unique FROM $this->table WHERE $this->unique='%d' LIMIT 1",$this->{$this->unique});
				$rows = WPDU_Database::Query($this->query, $connection);
			}
			
			$data['wpdu_rule_title'] = $this->String($this->wpdu_rule_title);
			$data['wpdu_user_roles'] = $this->Escape($this->wpdu_user_roles);
			$data['wpdu_user_incexe'] = $this->String($this->wpdu_user_incexe);
			$data['wpdu_author_id'] = $this->String($this->wpdu_author_id);
			$data['wpdu_user_name'] = $this->String($this->wpdu_user_name);
			$data['wpdu_user_email'] = $this->String($this->wpdu_user_email);
			$data['wpdu_user_description'] = $this->String($this->wpdu_user_description);
			$data['wpdu_user_website'] = $this->String($this->wpdu_user_website);
			$data['wpdu_user_limit'] = $this->String($this->wpdu_user_limit);
			$data['wpdu_order_by'] = $this->String($this->wpdu_order_by);
			$data['wpdu_order'] = $this->String($this->wpdu_order);
			
			if ($rows > 0 )
			{
				$where[$this->unique]=$this->Escape($this->{$this->unique});
			}
			else
			{
				$where = '';
			}
		
			$insertId = WPDU_Database::InsertOrUpdate($this->table,$data,$where);
			
			if ($this->{$this->unique} == "")
			{
				$this->{$this->unique} = $insertId;
			}
			return $this->{$this->unique};
		}
		
		function Delete() {

			$connection = WPDU_Database::Connect();
			$this->query = $connection->prepare("DELETE FROM $this->table WHERE $this->unique='%d'",$this->{$this->unique});
			return WPDU_Database::NonQuery($this->query, $connection);
		}
		
		
		public function du_form($view) {  

			$response = $this->do_action();
			
			switch($view) {
			  
			  	case 'create_rule' : $view = "wpdu-create-user-rule.php";
									    		include( WPDU_FORMS . '/'.$view );				
			  				 		    		break;
				
			  	case 'edit_rule' : $this->Get(array(array('wpdu_id','=',$this->wpdu_id)));
											  $view = "wpdu-create-user-rule.php"; 
											  include( WPDU_FORMS . '/'.$view );				
											  break;
										
			  	case 'manage_rule' : $view = "wpdu-manage-user-rule.php"; 									
									   include( WPDU_FORMS . '/'.$view );
			  					       break;			 
			}
		}
		 
		protected function do_action() {

			global $_POST, $DU_OBJ;
			 
			if( isset($_POST['submit']) ) {
				
				if(isset($_GET['action']) and $_GET['action']=="edit")
				$this->wpdu_id = $_GET['wpdu_id'];
				
				$DU_OBJ->setVal('wpdu_rule_title',htmlspecialchars(stripslashes($_POST['wpdu_rule_title'])));
				
				$DU_OBJ->setVal('wpdu_user_roles',serialize($_POST['wpdu_user_roles']));
				
				$DU_OBJ->setVal('wpdu_user_name',htmlspecialchars(stripslashes($_POST['wpdu_user_name'])));
				
				$DU_OBJ->setVal('wpdu_user_incexe',htmlspecialchars(stripslashes($_POST['wpdu_user_incexe'])));
				
				$DU_OBJ->setVal('wpdu_author_id',htmlspecialchars(stripslashes($_POST['wpdu_author_id'])));
				
				$DU_OBJ->setVal('wpdu_user_email',htmlspecialchars(stripslashes($_POST['wpdu_user_email'])));
				
				$DU_OBJ->setVal('wpdu_user_description',htmlspecialchars(stripslashes($_POST['wpdu_user_description'])));
				
				$DU_OBJ->setVal('wpdu_user_website',htmlspecialchars(stripslashes($_POST['wpdu_user_website'])));
				
				$DU_OBJ->setVal('wpdu_user_limit',htmlspecialchars(stripslashes($_POST['wpdu_user_limit'])));
								
				$DU_OBJ->setVal('wpdu_order_by',htmlspecialchars(stripslashes($_POST['wpdu_order_by'])));
				
				$DU_OBJ->setVal('wpdu_order',htmlspecialchars(stripslashes($_POST['wpdu_order'])));
				
								
				if($DU_OBJ->save()>0)
				{	
					if( isset($_GET['action']) and $_GET['action']=="edit" ) {
						
						$response["success"]  =__( 'Rule Updated Successfully.', 'wp-widget-bundle' );	
					}
					else
					{
						$response["success"]  = __( 'Rule Created successfully.', 'wp-widget-bundle' );
					}
					
					$_POST = array();		
				}
				else
				{
					$response["error"] = $this->display_errors();
				}
				
				return $response;	
			}	 
		}	
	}
}

function _wpdu_init() {
  	
  	global $DU_OBJ;
  	$DU_OBJ = new WPDU_DU();
}

add_action('plugins_loaded', '_wpdu_init');