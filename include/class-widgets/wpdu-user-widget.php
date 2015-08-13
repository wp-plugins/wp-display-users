<?php
/**
* @version 1.0.0
* @package WP Display Users
*/

class WPDU_UW extends WP_Widget {

	private $username;
    private $email;
    private $password;
    private $website;
    private $first_name;
    private $last_name;
    private $nickname;
    private $bio;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'wp_display_users', // Base ID
			__( 'WP Display Users', 'wp-display-users' ), // Name
			array( 'description' => __( 'A widget that displays the users.', 'wp-display-users' ) ) // Args
		);
	}
	
	public function widget( $args, $instance ) { 
		
		global $wpdb;
		$query = 'SELECT * FROM '.TBL_DU.' WHERE wpdu_id='.$instance['wpdu_id'];
		$user_list_record = $wpdb->get_row( $query );
		
		if( empty($user_list_record) ) return;
		
		$options = get_option( '_wpdu_settings' );
		$options = unserialize($options);
		
		$wpdu_content_word_limit = ( $options['wpdu_content_word_limit'] != "" ) ? sanitize_text_field( $options['wpdu_content_word_limit'] ) : '25';
		
		$wpdu_display_pagination_widget = ( $options['wpdu_display_pagination_widget'] != "" ) ? sanitize_text_field( $options['wpdu_display_pagination_widget'] ) : 'false';
		
		?>
        <div class="wpdu_container">
            
            <?php if ( isset( $instance['title'] ) ) { ?>
                <div class="wpdu_wiget_title">
                
                    <h1 class="wpdu_title"><?php echo $instance['title']; ?></h1>
                 
                </div>   
            <?php } ?>
            
            <?php
			$unserialize_user_roles = unserialize(stripslashes($user_list_record->wpdu_user_roles));
				
				if( !empty($unserialize_user_roles) ) {
					
					if( is_array($unserialize_user_roles) ) {
						
						echo '<div class="wpdu_contant"><ul class="wpdu-user-list">';
						
						foreach ($unserialize_user_roles as $unserialize_user_role) {
							
							$user_args['role'] = $unserialize_user_role;
							$user_args['orderby'] = $user_list_record->wpdu_order_by;
							$user_args['order']   = $user_list_record->wpdu_order;
							
							if( $user_list_record->wpdu_user_incexe == 'include' ) {

								if( !empty($user_list_record->wpdu_author_id) )
								$user_args['include'] = $user_list_record->wpdu_author_id;
								else
								$user_args['include'] = '';
							}
							
							if( $user_list_record->wpdu_user_incexe == 'exclude' ) {
								
								if( !empty($user_list_record->wpdu_author_id) )
								$user_args['exclude'] = $user_list_record->wpdu_author_id;
								else
								$user_args['exclude'] = '';
							}
							
							if( !empty($user_list_record->wpdu_user_limit) ) {
				
								$number = $user_list_record->wpdu_user_limit; 
							}
							else
							{
								$number = 10;
							} 
							
							if( $wpdu_display_pagination_widget == 'true' ) { 
							
								if ( get_query_var('paged') ) { 
									
									$paged = get_query_var('paged'); 
								}
								else if( get_query_var('page') ) { 
								
									$paged = get_query_var('page'); 
								}
								else 
								{ 
									$paged = 1; 
								}
								
								$offset = ($paged - 1) * $number;
								$user_args['offset'] = $offset; 
							}
							
							$user_args['number'] = $number;
							$user_query = new WP_User_Query( $user_args );
							
							if ( ! empty( $user_query->results ) ) {
								
								foreach ( $user_query->results as $user ) {
									
									$display_name = get_the_author_meta('display_name', $user->ID);
									$description = get_the_author_meta('description', $user->ID);
									$website = get_the_author_meta('url', $user->ID);
									$email = get_the_author_meta('email', $user->ID);
									
									$match_src = "/src=[\"' ]?([^\"' >]+)[\"' ]?[^>]*>/i" ;
									$avatar = get_avatar($user->ID); // for example
									preg_match($match_src, $avatar, $matches);
									$avatar_src = $matches[1];
									?>
										<li class="wpdu-user">
                        					<span class="wpdu-user-avatar">
												<img src="<?php echo $avatar_src; ?>" />							
											</span>
											<span class="wpdu-user-single">
												<?php if( !empty($user_list_record->wpdu_user_name) && $user_list_record->wpdu_user_name=='true') : ?>
                                                    <span class="wpdu-user-author">
                                                         
                                                            <?php
                                                            if( !empty($display_name) )
                                                            { 
                                                                echo $display_name; 
                                                            }
                                                            ?>  
                                                        
                                                     </span>   
                                                  <?php endif; ?> 
                                                
												<?php 
                                                 if( !empty($user_list_record->wpdu_user_description) && $user_list_record->wpdu_user_description=='true') : 
                                                    ?>		      
                                                    <span class="wpdu-user-text">
                                                           { "<?php
                                                               if( !empty($description) )
                                                               {
                                                                   echo $this->wpdu_user_widget_excerpt($description, $wpdu_content_word_limit);
                                                               }
                                                           ?> " }
                                                    </span>
                                                <?php endif; ?> 
                                                
                                                <?php 
									if( !empty($user_list_record->wpdu_user_email) && $user_list_record->wpdu_user_email=='true') 
									{
										?>
                                        <span class="wpdu-user-email">
                                        <?php
										if( !empty($email) )
										{ 
											echo '<br/><strong>Email : </strong>'.$email; 													                                        }
										?>
                                        </span>
                                        <?php    
									}
								 
									if( !empty($user_list_record->wpdu_user_website) && $user_list_record->wpdu_user_website=='true') 
									{
										?>
                                        <span class="wpdu-user-url">
                                        <?php
										if( !empty($website) )
										{ 
											echo '<br/><strong>Website : </strong>'.$website;
										}
										?>
                                        </span>
                                        <?php
									}
                                    ?>
                                            
									<?php
									
								}
							}
						}
						echo '</ul>';
						
						
						if( $wpdu_display_pagination_widget == 'true' ) { 
							
							$total_user = $user_query->total_users;  
							$total_pages = ceil($total_user / $number);
							
							echo '<div class="wpdu-pagination" class="clearfix">';
								  $current_page = max(1, get_query_var('paged'));
								  echo paginate_links(array(
										'base' => get_pagenum_link(1) . '%_%',
										'format' => 'page/%#%/',
										'current' => $current_page,
										'total' => $total_pages,
										'prev_next'    => true,
										'type'         => 'list',
									));
							echo '</div>';
						}
						
						echo '</div>';
					}
				}
             ?>
        </div>
        <?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['wpdu_id'] = ( ! empty( $new_instance['wpdu_id'] ) ) ? strip_tags( $new_instance['wpdu_id'] ) : '';

		return $instance;
	}

	public function form( $instance ) {
		
		global $wpdb;
	 	$wpdu_rules = $wpdb->get_results("SELECT * FROM ".TBL_DU."");
	 
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} 
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
			       value="<?php echo esc_attr( $title ); ?>">
		</p>
        <p>
            <label for="<?php echo $this->get_field_id('wpdu_id');?>" style="font-weight:bold;">Select Rule : </label> 
            <select id="<?php echo $this->get_field_id('wpdu_id'); ?>" name="<?php echo $this->get_field_name( 'wpdu_id' ); ?>" style="width:80%;">
            <option value="">Select Rule</option>
            <?php 
            if($wpdu_rules) {
                foreach($wpdu_rules as $wpdu_rule){  ?>
                    <option value="<?php echo $wpdu_rule->wpdu_id; ?>"<?php selected($wpdu_rule->wpdu_id,$instance['wpdu_id']); ?>><?php echo $wpdu_rule->wpdu_rule_title; ?></option>
            <?php 
                }
            } 
            ?>	
            </select>
        </p> 
	<?php
	}
	
	/**
		 * Trim a text to a certain number of words, adding a dotdotdot if necessary, and add break to long words.
		 *
		 * @param	string	$text	The text to trim
		 * @param	int		$length	The length you want to trim to
		 * @param	boolean	$chunk Split long words to chunks of this length
		 * @param	boolean	$autop	Automatically add paragraph
		 */
		function wpdu_user_widget_excerpt($text = '', $length = 50, $chunk = 0, $autop = false) {
			
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
} // class WPDU_UW