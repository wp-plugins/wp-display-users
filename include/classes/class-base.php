<?php
/**
* Class : WPDU_Plugin_Base
* @version 1.0.0
* @package WP Display Users
*/

if ( !class_exists( 'WPDU_Plugin_Base' ) ) {

	class WPDU_Plugin_Base {
		
		protected $errors;	
		protected $success;
		private $query;
		public $table;
		public $unique;
		
		private function WPDU_Plugin_Base() {
			
		}

		function getVal($property) {

			if( property_exists($this, $property) ) {

				return $this->Unescape($this->Unescape($this->{$property}));
			}
		}
		
		function setVal($property, $value) {

			if(is_array($value))
			$this->{$property} = $value;
			elseif($this->valid($property,$value))
			$this->{$property} = $value;
		}
		
		function valid($property, $value) {
			
			if( property_exists($this, $property) ) {
				
				$validator = new WPDU_Validator();
			
				if( isset($this->valiations[$property]) ) {
					
					foreach($this->valiations[$property] as $type => $message) {

						$validator->add($property,$value,$type,$message);				
					}
				
					$errors = $validator->validate();
				
					if($errors)
					{
						$this->errors[$property]=$errors[$property];
						return false;
					}
					else
					{
						return true;
					}
				}
				else
				{
					return true;
				}		
			}
		}
		
		function Get($table = '', $fcv_array = array(), $sortBy = '', $ascending = true, $limit = '') {
			
			$connection = WPDU_Database::Connect();
			
			$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
			$this->query = "SELECT * FROM $this->table ";		
			$ruleList = Array();
			$objects  = Array();
			
			if (sizeof($fcv_array) > 0) {	

				$this->query .= " WHERE ";
				
				for ($i=0, $c=sizeof($fcv_array); $i<$c; $i++) {
					
					if (sizeof($fcv_array[$i]) == 1)
					{
						$this->query .= " ".$fcv_array[$i][0]." ";
						continue;
					}
					else
					{					
						if ($i > 0 && sizeof($fcv_array[$i-1]) != 1)
						{
							$this->query .= " AND ";
						}
						if (isset($this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
						{
							if ($GLOBALS['configuration']['db_encoding'] == 1)
							{
								$value = WPDU_Plugin_Base::IsColumn($fcv_array[$i][2]) ? "BASE64_DECODE(".$fcv_array[$i][2].")" : "'".$fcv_array[$i][2]."'";
								$this->query .= "BASE64_DECODE(`".$fcv_array[$i][0]."`) ".$fcv_array[$i][1]." ".$value;
							}
							else
							{
								$value =  WPDU_Plugin_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : "'".$this->Escape($fcv_array[$i][2])."'";
								$this->query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
							}
						}
						else
						{
							$value = WPDU_Plugin_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : "'".$fcv_array[$i][2]."'";
							$this->query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
						}
					}
				}
			}
		
			if ( !empty($sortBy) )
			{
				if (isset($this->pog_attribute_type[$sortBy]['db_attributes']) && $this->pog_attribute_type[$sortBy]['db_attributes'][0] != 'NUMERIC' && $this->pog_attribute_type[$sortBy]['db_attributes'][0] != 'SET')
				{
					if ($GLOBALS['configuration']['db_encoding'] == 1)
					{
						$sortBy = "BASE64_DECODE($sortBy) ";
					}
					else
					{
						$sortBy = "$sortBy ";
					}
				}
				else
				{
					$sortBy = "$sortBy ";
				}
			}
			else
			{
				$sortBy = $this->unique;
			}
			
			$this->query .= " ORDER BY ".$sortBy." ".($ascending ? "ASC" : "DESC")." $sqlLimit";
			
			$thisObjectName = get_class($this);
			$cursors = WPDU_Database::Reader($this->query, $connection);
			
			foreach( $cursors as $row) {
				
				$obj = new $thisObjectName();
				$obj->fill($row);
				$objects[] = $obj;
			}
			
		   	return $objects;
		}
		
		function Query($query) {	

			$this->query = $query;
			$connection = WPDU_Database::Connect();
			$thisObjectName = get_class($this);
			$cursors = WPDU_Database::Reader($this->query, $connection);
			
			if( !empty($cursors) ) {

				foreach( $cursors as $row) {
					
					$obj = new $thisObjectName();
					$obj->fill($row);
					$objects[] = $obj;
				}

				return $objects;
			}
		}

		protected function display_errors() {
			
			if(isset($this->errors) and is_array($this->errors)) {

				return implode('<br>',$this->errors);			
			}	
		}

		/**
		* This function will try to encode $text to base64, except when $text is a number. This allows us to Escape all data before they're inserted in the database, regardless of attribute type.
		* @param string $text
		* @return string encoded to base64
		*/
		public function Escape($text) {
			
			return mysql_real_escape_string($text);
		}

		public function String($text) {
			
			return htmlspecialchars(stripslashes($text));
		}

		/**
		 * Enter description here...
		 *
		 * @param unknown_type $text
		 * @return unknown
		 */
		public function Unescape($text) {

			return stripcslashes($text);
		}

		private function PopulateObjectAttributes($fetched_row, $pog_object) {

			$att = $this->GetAttributes($pog_object);
	 		
	 		foreach ($att as $column) {

				$pog_object->{$column} = $this->Unescape($fetched_row[strtolower($column)]);
			}

			return $pog_object;
		}

		public function GetAttributes($object, $type='') {

			$columns = array();

			foreach ($object->pog_attribute_type as $att => $properties) {
				
				if ($properties['db_attributes'][0] != 'OBJECT')
				{
					if (($type != '' && strtolower($type) == strtolower($properties['db_attributes'][0])) || $type == ''){
						$columns[] = $att;
					}
				}
			}

			return $columns;
		}
		
		public static function IsColumn($value) {

			if (strlen($value) > 2)
			{
				if (substr($value, 0, 1) == '`' && substr($value, strlen($value) - 1, 1) == '`')
				{
					return true;
				}
				return false;
			}

			return false;
		}
		
		public function show_message($message, $errormsg = false) {

			if( empty($message) )
			return;
			
			if ( $errormsg ) {
				
				echo '<div id="message" class="wpdu_error">';
			}
			else {

				echo '<div id="message" class="wpdu_update">';
			}

			echo "<p><strong>$message</strong></p></div>";
		}
	}
}	