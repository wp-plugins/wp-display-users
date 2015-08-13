<?php
/**
* @version 1.0.0
* @package WP Display Users
*/

  if( isset($_POST['setting_restore_submit']) ) {
            
            $wpdu_restore_value_array = array(
			    "wpdu_username_font_size" =>  25,
                "wpdu_username_text_transform" =>  "uppercase",
                "wpdu_content_font_size" =>  13,
                "wpdu_content_word_limit" =>  25,
				"wpdu_content_word_limit" =>  "false"
            );

            $wpdu_restore_setting_value = serialize($wpdu_restore_value_array);
            $wpdu_option = "_wpdu_settings";
            update_option( $wpdu_option, $wpdu_restore_setting_value );

            $message =  __( "Settings restore Successfully.", "wpdu_widgets");
        }    

        if( isset($_POST["setting_submit"]) ) {

            $wpdu_value_array = array(
			    "wpdu_username_font_size"   =>  $_POST["wpdu_username_font_size"],
                "wpdu_username_text_transform" =>  $_POST["wpdu_username_text_transform"],
                "wpdu_content_font_size"     =>  $_POST["wpdu_content_font_size"],
                "wpdu_content_word_limit"    =>  $_POST["wpdu_content_word_limit"], 
				"wpdu_display_pagination_widget" => $_POST["wpdu_display_pagination_widget"] 
            );
            
            $wpdu_setting_value = serialize($wpdu_value_array);
            $wpdu_option = "_wpdu_settings";
            $deprecated = "";
            $autoload = true;
            if( get_option( $wpdu_option ) ) 
            {
                $wpdu_new_value = $wpdu_setting_value;
                update_option( $wpdu_option, $wpdu_new_value );
            } 
            else 
            {
                add_option( $wpdu_option, $wpdu_setting_value, $deprecated, $autoload );   
            } 

            $message =  __( "Settings save Successfully.", "wpdu_widgets");
        }   

        $options = get_option( '_wpdu_settings' );
        $options = unserialize($options);
		
		$wpdu_username_font_size = ( $options['wpdu_username_font_size'] != "" ) ? sanitize_text_field( $options['wpdu_username_font_size'] ) : '25';
        $wpdu_username_text_transform = ( $options['wpdu_username_text_transform'] != "" ) ? sanitize_text_field( $options['wpdu_username_text_transform'] ) : 'uppercase';
        $wpdu_content_font_size = ( $options['wpdu_content_font_size'] != "" ) ? sanitize_text_field( $options['wpdu_content_font_size'] ) : '13';
        $wpdu_content_word_limit = ( $options['wpdu_content_word_limit'] != "" ) ? sanitize_text_field( $options['wpdu_content_word_limit'] ) : '25';
		$wpdu_display_pagination_widget = ( $options['wpdu_display_pagination_widget'] != "" ) ? sanitize_text_field( $options['wpdu_display_pagination_widget'] ) : 'false';
  	?>
    
    <?php
	
	if( !empty($message) ) {
		
		echo '<div id="message" class="wpdu_update">';
		echo '<p><strong>';
		echo $message;
		echo '</strong></p>';
		echo '</div>';
	}
	?>

  <form method="post" action="">
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"> <label for="wpdu_username_font_size">
              <?php _e( 'Font Size', 'wp-display-users' ); ?>
            </label>
          </th>
          <td><input type="text" name="wpdu_username_font_size" id="wpdu_username_font_size" class="regular-text wpwb-color-field" value="<?php echo $wpdu_username_font_size; ?>">
            <p class="description">
              <?php _e( 'Please enter here { Username Font Size }. default is 25px', 'wp-display-users' ); ?>
            </p></td>
        </tr>
        <tr valign="top">
          <th scope="row"> <label for="wpdu_username_text_transform">
              <?php _e( 'Text Transform', 'wp-display-users' ); ?>
            </label>
          </th>
          <td>
          <select name="wpdu_username_text_transform" id="wpdu_username_text_transform">
             
              <option value="none"<?php selected($wpdu_username_text_transform,"none"); ?>><?php _e( 'none', 'wp-display-users' ); ?></option>
              
              <option value="capitalize"<?php selected($wpdu_username_text_transform,"capitalize"); ?>><?php _e( 'capitalize', 'wp-display-users' ); ?></option>
              
              <option value="lowercase"<?php selected($wpdu_username_text_transform,"lowercase"); ?>><?php _e( 'lowercase', 'wp-display-users' ); ?></option>
              
              <option value="uppercase"<?php selected($wpdu_username_text_transform,"uppercase"); ?>><?php _e( 'uppercase', 'wp-display-users' ); ?></option>
      				
           </select>
                
            <p class="description">
              <?php _e( 'Please select text transform for { Username }. default is uppercase', 'wp-display-users' ); ?>
            </p></td>
        </tr>
      <tr valign="top">
        <th scope="row"> <label for="wpdu_content_font_size">
            <?php _e( 'Content Font Size', 'wp-display-users' ); ?>
          </label>
        </th>
        <td><input type="text" name="wpdu_content_font_size" id="wpdu_content_font_size" class="regular-text" value="<?php echo $wpdu_content_font_size; ?>">&nbsp;px
          <p class="description">
            <?php _e( 'Please enter here font size for { Content }. default is 13px', 'wp-display-users' ); ?>
          </p></td>
      </tr>
      <tr valign="top">
        <th scope="row"> <label for="wpdu_content_word_limit">
            <?php _e( 'Content Word Limit', 'wp-display-users' ); ?>
          </label>
        </th>
        <td><input type="text" name="wpdu_content_word_limit" id="wpdu_content_word_limit" class="regular-text" value="<?php echo $wpdu_content_word_limit; ?>">&nbsp;px
          <p class="description">
            <?php _e( 'Please enter here { Content Word Limit }. default is 25', 'wp-display-users' ); ?>
          </p></td>
      </tr>
      <tr valign="top">
        <th scope="row"> <label for="wpdu_display_pagination_widget">
            <?php _e( 'Pagination on Widget', 'wp-display-users' ); ?>
          </label>
        </th>
        <td>
        	<p class="description">
        	<input type="checkbox" name="wpdu_display_pagination_widget" id="wpdu_display_pagination_widget" value="true"<?php checked($wpdu_display_pagination_widget,"true"); ?>>
            <?php _e( 'Please check to enable pagination on widgets.', 'wp-display-users' ); ?>
          </p>
        </td>
      </tr>
      <tr valign="top">
        <td colspan="2"> 
        <input type="submit" name="setting_submit" id="submit" class="button button-primary" value="Save"> 
        <input type="submit" name="setting_restore_submit" id="submit" class="button button-primary" value="Restore Default">    
        </td>
      </tr>
      </tbody>
    </table>
  </form>