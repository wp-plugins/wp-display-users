<?php
/**
 * @package WP Display Users
 * @version 1.0.0
*/

/*
Plugin Name: WP Display Users
Plugin URI: https://github.com/devnathverma/wp-display-users/
Description: A plugin used for display user listing on post, page and sidebar widgets.
Author: Devnath verma
Author Email: devnathverma@gmail.com
Version: 1.0.0
Text Domain: wp-display-users
Domain Path: /lang/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2008-2015 Devnath verma (devnathverma@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if( !defined('ABSPATH') ) {
	
	die( 'You are not allowed to call this page directly.' );
}

if( !class_exists('WP_Display_Users') ) {
	
	class WP_Display_Users {
		
		/**
		* Construct the plugin object
		* @version 1.0.0
		* @package WP Display Users
		*/			 
		public function __construct() {
			
			// Installation
			register_activation_hook( __FILE__, array( $this, 'wpdu_network_propagate' ) );
			add_action( 'init', array($this, '_wpdu_init') );
			add_action( 'widgets_init' , array(&$this, 'wpdu_display_widget') );
			$this->_wpdu_define_constants();
		    $this->_wpdu_load_files();
		}
		
		/**
	    * Register activation for single and multisites
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function wpdu_network_propagate($network_wide) {
			
			if ( is_multisite() && $network_wide ) { 
				
				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated = array();
		 
				$sql = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids = $wpdb->get_col($wpdb->prepare($sql,null));
				foreach ($blog_ids as $blog_id) {
					switch_to_blog($blog_id);
					$this->wpdu_activate();
					$activated[] = $blog_id;
				}
	 
				switch_to_blog($currentblog);
				update_site_option('wpdu_activated', $activated);
			} 
			else 
			{
				$this->wpdu_activate();
			}
		}
		
		/**
	    * Create table used in plugin
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function wpdu_activate() {
			
			global $wpdb;
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			
			$wpdu_du = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."du` (
						`wpdu_id` int(11) NOT NULL AUTO_INCREMENT,
						`wpdu_rule_title` varchar(255) NOT NULL,
						`wpdu_user_roles` text DEFAULT NULL,
						`wpdu_user_name` varchar(255) NOT NULL,
						`wpdu_user_email` varchar(255) NOT NULL,
						`wpdu_user_description` text DEFAULT NULL,
						`wpdu_user_website` varchar(255) NOT NULL,
						`wpdu_user_limit` varchar(255) NOT NULL,
						`wpdu_user_incexe` varchar(255) NOT NULL,
						`wpdu_author_id` varchar(255) NOT NULL,
						`wpdu_order_by` varchar(255) NOT NULL,
						`wpdu_order` varchar(255) NOT NULL,
						 PRIMARY KEY (`wpdu_id`)
						) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			dbDelta( $wpdu_du );
		}
		
		/**
	    * Define paths
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function _wpdu_define_constants() {
			
			global $wpdb;

	    	if ( !defined( 'WPDU_VERSION' ) )
				define('WPDU_VERSION', '1.0.0');

			if ( !defined( 'WPDU_FOLDER' ) )
				define('WPDU_FOLDER', basename(dirname(__FILE__)));
			
			if ( !defined( 'WPDU_DIR' ) )
				define('WPDU_DIR', plugin_dir_path(__FILE__));
			
			if ( !defined( 'WPDU_INC' ) )
				define('WPDU_INC', WPDU_DIR.'include'.'/');
				
			if ( !defined( 'WPDU_CLASS' ) )
				define('WPDU_CLASS', WPDU_INC.'classes'.'/');
				
			if ( !defined( 'WPDU_CLASS_WIDGET' ) )
				define('WPDU_CLASS_WIDGET', WPDU_INC.'class-widgets');
				
			if ( !defined( 'WPDU_SHORTCODE' ) )
				define('WPDU_SHORTCODE', WPDU_INC.'shortcodes');		
				
			if ( !defined( 'WPDU_FORMS' ) )
				define('WPDU_FORMS', WPDU_INC.'forms');
				
			if ( !defined( 'WPDU_FUNCTION' ) )
				define('WPDU_FUNCTION', WPDU_INC.'function'.'/');
			
			if ( !defined( 'WPDU_URL' ) )
				define('WPDU_URL', plugin_dir_url(WPDU_FOLDER).WPDU_FOLDER.'/');
			
			if ( !defined( 'WPDU_CSS' ) )
				define('WPDU_CSS', WPDU_URL.'assets/css'.'/');
			
			if ( !defined( 'WPDU_JS' ) )
				define('WPDU_JS', WPDU_URL.'assets/js'.'/');
			
			if ( !defined( 'WPDU_IMAGES' ) )
				define('WPDU_IMAGES', WPDU_URL.'assets/images'.'/');
			
			if ( !defined( 'WPDU_FONTS' ) )
				define('WPDU_FONTS', WPDU_URL.'assets/fonts'.'/');
			
			if ( !defined( 'WPDU_ICONS' ) )	
				define('WPDU_ICONS', WPDU_URL.'assets/icons'.'/');
			
			if ( !defined( 'TBL_DU' ) )
				define('TBL_DU', $wpdb->prefix.'du');
		}
		
		/**
	    * Required files includes in plugin
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function _wpdu_load_files() { 
			
			if( !class_exists( 'WP_List_Table' ) )
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
			
			require_once( WPDU_CLASS.'class-validation.php' );
			require_once( WPDU_CLASS.'class-database.php' );
			require_once( WPDU_CLASS.'class-base.php' ); 
			require_once( WPDU_CLASS.'class-du.php' );
		   
			require_once( WPDU_CLASS_WIDGET.'/wpdu-user-widget.php' );
	  	}
		
		/**
	    * Call wordpress actions
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function _wpdu_init() { 
			
			add_action( 'admin_menu', array(&$this, 'wpdu_admin_menu') );
			add_action( 'wp_head', array(&$this, 'wpdu_load_head_section') );
			add_action( 'admin_enqueue_scripts', array(&$this, 'wpdu_load_scripts_backend') );
			add_action( 'wp_enqueue_scripts' , array(&$this, 'wpdu_load_scripts_frontend') );
			add_shortcode( 'wp_display_user', array(&$this, 'wpdu_return_shortcode_data') );
		}
		
		/**
	    * Register required widgets
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function wpdu_display_widget() {

			register_widget( 'WPDU_UW' );
		}
		
		/**
		* This function used to create menus on admin section.
		* @version 1.0.0
		* @package WP Display Users
		*/	
        public function wpdu_admin_menu() {
			
			// Create Admin Menus
			add_menu_page(
				__('WP Display Users', "wp-display-users"), 
				__('WP Display Users', "wp-display-users"), 
				'manage_options', 
				'wp-display-users', 
				array(&$this, 'wpdu_display_users')
			);
		}
		
		
		/**
	    * Create tabs menu used in plugin
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function wpdu_display_users() {
        
			$menu_tabs = array(
                'manage-rule' => __( 'Manage Rules', 'wp-display-users' ),
                'add-rule' => __( 'Add Rule', 'wp-display-users' ),             
                'settings' => __( 'Settings', 'wp-display-users' )
            );
        ?>
            <h2>
                <span class="glyphicon glyphicon-asterisk"></span>
                <?php _e('WP Display Users', 'wp-display-users')?>
            </h2>

            <?php
			
            echo '<ul id="wpdu-main-nav" class="nav-tab-wrapper">';
            
            if( !empty($_GET['tab']) ) {

                $current_tab = $_GET['tab']; 
            
            } else {

                $current_tab = 'manage-rule'; 
            }

            foreach($menu_tabs as $tab_key => $tab_title ) {
              
                $active_tab = '';
              
                if( $current_tab == $tab_key ) 
                {                           
                    $active_tab = 'nav-tab-active';
                }
              
                echo '<li>';
                echo '<a class="nav-tab ' . $active_tab . '" href="'.admin_url('admin.php?page=wp-display-users&tab='.$tab_key).'">'. $tab_title .'</a>';
                echo '</li>';
            }

            echo '</ul>';
            
            if( !empty($current_tab) ) {

                switch( $current_tab ) {
                    
                    case 'manage-rule' : $this->wpdu_tab_manage_rule(); break;   
                 
                    case 'add-rule' : $this->wpdu_tab_add_rule(); break;
                  
                    case 'settings' : $this->wpdu_tab_settings(); break;

                    default : $this->wpdu_tab_manage_rule();            
                }

            } else {

                $this->wpdu_tab_manage_rule();
            }
        }
		
		/**
	    * Load scripts and css in head sections
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function wpdu_load_head_section() {
			
			$options = get_option( '_wpdu_settings' );
			$options = unserialize($options);
			
			$wpdu_username_font_size = ( $options['wpdu_username_font_size'] != "" ) ? sanitize_text_field( $options['wpdu_username_font_size'] ) : '25';
			
			$wpdu_username_text_transform = ( $options['wpdu_username_text_transform'] != "" ) ? sanitize_text_field( $options['wpdu_username_text_transform'] ) : 'uppercase';
			
			$wpdu_content_font_size = ( $options['wpdu_content_font_size'] != "" ) ? sanitize_text_field( $options['wpdu_content_font_size'] ) : '13';
			
			echo '
			<style>
			div.wpdu-user-container h3 {
				text-transform: '.$wpdu_username_text_transform.';
				font-size: '.$wpdu_username_font_size.'px;
			}
			div.wpdu-user-container h4 {
				font-size: '.$wpdu_content_font_size.'px;
			}
			</style>
			';
		}
		
		/**
	    * Manages all user rules
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function wpdu_tab_manage_rule() {

            global $DU_OBJ;
            echo $DU_OBJ->du_form('manage_rule');
        }
		
		/**
	    * Add user rule
	    * @version 1.0.0
		* @package WP Display Users
	    */
        public function wpdu_tab_add_rule() {
        
            global $DU_OBJ; 
            echo $DU_OBJ->du_form('create_rule');
        }
		
		/**
	    * Includes shortcode file for front view used in plugin
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function wpdu_return_shortcode_data($atts) {
			
		 	 ob_start();
			 require_once( WPDU_SHORTCODE.'/wpdu-shortcode.php' );
			 $content = ob_get_contents();
	         ob_clean();
	      	 return $content;
		}
		
		/**
	    * Includes slider settings form used in plugin
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function wpdu_tab_settings() {
			
			include( WPDU_FORMS . '/wpdu-settings.php');
		}
					
		/**
		* This function used to load text domain for multilanguages.
		* @version 1.0.0
		* @package WP Display Users
		*/	
		public function wpdu_load_languages() {
			
		 	// Load Text Domain
			load_plugin_textdomain( 'wp-display-users', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );
		}
		
		/**
		 * Trim a text to a certain number of words, adding a dotdotdot if necessary, and add break to long words.
		 *
		 * @param	string	$text	The text to trim
		 * @param	int		$length	The length you want to trim to
		 * @param	boolean	$chunk Split long words to chunks of this length
		 * @param	boolean	$autop	Automatically add paragraph
		 */
		function wpdu_user_excerpt($text = '', $length = 50, $chunk = 0, $autop = false) {
			
			if (empty($text))
			return '';
			
			// ensure that no comment has double spaces
			$text = trim($text);
			$text = preg_replace('/\s+/iu', ' ', $text);
			$actual_length = count(explode(' ', $text));
			$dotdotdot = ($actual_length > $length) ? apply_filters('wpdu_dotdotdot', '.....') : '';
			$words = explode(' ', $text, $length + 1);
	
			if (count($words) > $length)
			array_pop($words);
	
			if (!empty($chunk))
			{
				$text = '';
				foreach ($words as $word)
				{
					$tmp = preg_split("//u", $word, -1, PREG_SPLIT_NO_EMPTY);
					if (0 < sizeof($tmp))
					{
						$wl = sizeof($tmp);
						if ($chunk < $wl)
						{
							$text_chunked = array_chunk($tmp, $chunk);
							foreach ($text_chunked as $chunked)
							{
								$text .= implode('', $chunked) . ' ';
							}
						}
						else
							$text .= $word . ' ';
					}
				}
			}
			else
			{
				$text = implode(' ', $words);
			}
			
			$text .= $dotdotdot;
	
			if ($autop == true) $text = wpautop($text);
			
			return trim($text);
		}
		
		/**
	    * Load JS and CSS in backend
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function wpdu_load_scripts_backend() {

            if( is_admin() ) { 
            
                //Access the global $wp_version variable to see which version of WordPress is installed.
                global $wp_version;

                wp_enqueue_media();
                
                //If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
                if ( 3.5 <= $wp_version ){
                    
                    //Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
                    wp_enqueue_style( 'wp-color-picker' );
                    wp_enqueue_script( 'wp-color-picker' );
                }
                //If the WordPress version is less than 3.5 load the older farbtasic color picker.
                else {
                  
                    //As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
                    wp_enqueue_style( 'farbtastic' );
                    wp_enqueue_script( 'farbtastic' );
                }
                
                // Include our wsl store locator css file
                wp_enqueue_style( 'wpdu-backend-css', WPDU_CSS.'wpdu-backend-css.css' );
            }
        }
		
		/**
	    * Load js and css in frontend section
	    * @version 1.0.0
		* @package WP Display Users
	    */
		public function wpdu_load_scripts_frontend() {
			
			wp_enqueue_style( 'wpdu-frontend-css', WPDU_CSS.'wpdu-frontend-css.css' );
		}
		
    } // END class WP_Display_Users
	
	/**
	* Initialize WP_Display_Users class
	*/
	$wpdu_advance_widgets = new WP_Display_Users();
	
} // END if(!class_exists('WP_Display_Users'))