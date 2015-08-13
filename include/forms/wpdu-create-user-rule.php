<?php 
/**
 * Required file for add user rule.
 * @version 1.0.0
 * @package WP Display Users
 */
?>

<?php require_once(WPDU_FUNCTION.'wpdu-function.php'); ?>

<form method="post" action="">
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"> 
                <label for="blogname">Rule Title&nbsp;<span style="color:#F00;">*</span></label>
          </th>
          <td>
            <input type="text" name="wpdu_rule_title" id="wpdu_rule_title" class="regular-text" value="<?php if( !empty($_POST['wpdu_rule_title']) ) { echo $_POST['wpdu_rule_title']; } ?>" />
            <p class="description">
              <?php _e( 'Please enter here rule title.', 'wp-display-users' ); ?>
            </p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"> 
                <label for="blogname">Choose Roles&nbsp;<span style="color:#F00;">*</span></label>
          </th>
          <td>
		  <?php
          global $wp_roles;
          $user_roles = $wp_roles->get_names();
          if( !empty($user_roles) )
          {
              foreach($user_roles as $key => $user_role)
              {
                  ?>
                    <p class="description">
                      <?php
                      if( empty($_POST['wpdu_user_roles']) )
                      {
                          ?>
                            <input type="checkbox" name="wpdu_user_roles[]" class="wpdu_check_user_id" value="<?php echo $user_role; ?>" />&nbsp;<?php echo $user_role; ?>
                          <?php
                      }
                      else
                      {
                          if( in_array($user_role, $_POST['wpdu_user_roles']) )
                          {
                    ?>
                                <input type="checkbox" name="wpdu_user_roles[]" class="wpdu_check_user_id" value="<?php echo $user_role; ?>" checked="checked" />&nbsp;<?php echo $user_role; ?>
                    <?php  
                          }
                          else
                          {
                              ?>
                                <input type="checkbox" name="wpdu_user_roles[]" class="wpdu_check_user_id" value="<?php echo $user_role; ?>" />&nbsp;<?php echo $user_role; ?>
                              <?php
                          }
                      }
                      ?>
                      </p>
                      <?php
                }
            }
            ?>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"> <label for="wpdu_user_incexe">
              <?php _e( 'Filter by Users IDs', 'wp-display-users' ); ?>
            </label>
          </th>
          <td>
          		<select name="wpdu_user_incexe" id="wpdu_user_incexe">
                  <option value="">None</option>	
                  <option value="include" <?php selected($_POST['wpdu_user_incexe'],'include'); ?>>Include</option>
                  <option value="exclude" <?php selected($_POST['wpdu_user_incexe'],'exclude'); ?>>Exclude</option>
                </select>
            <p class="description">
              <?php _e( 'Please choose any one option for include or exclude users.', 'wp-display-users' ); ?>
            </p></td>
        </tr>
        <tr valign="top">
          <th scope="row"> 
          	<label for="default_role"></label>
          </th>
          <td>
          		<input type="text" name="wpdu_author_id" id="wpdu_author_id" class="regular-text" value="<?php echo $_POST['wpdu_author_id']; ?>" />
                <p class="description">
                  <?php _e( 'Please enter here { Users IDs } by comma seprated. { Example : 1, 2, 3 }', 'wp-display-users' ); ?>
                </p>
            </td>
        </tr>
        <tr valign="top">
          <th scope="row"> 
                <label for="blogname">Display name</label>
          </th>
          <td>
                <p class="description">
                <input type="checkbox" name="wpdu_user_name" id="wpdu_user_name" value="true"<?php if( !empty($_POST['wpdu_user_name']) ) { checked($_POST['wpdu_user_name'],"true"); } ?> />
<?php _e( 'Please check to enable display name.', 'wp-display-users' ); ?>
                </p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"> 
                <label for="blogname">Display Email</label>
          </th>
          <td>
                <p class="description">
                <input type="checkbox" name="wpdu_user_email" id="wpdu_user_email" value="true" <?php if( !empty($_POST['wpdu_user_email']) ) { checked($_POST['wpdu_user_email'],"true"); } ?> /> 
                <?php _e( 'Please check to enable display email address.', 'wp-display-users' ); ?>
                </p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"> 
                <label for="blogname">Display Description</label>
          </th>
          <td>
                <p class="description">
                <input type="checkbox" name="wpdu_user_description" id="wpdu_user_description" value="true"<?php if( !empty($_POST['wpdu_user_description']) ) { checked($_POST['wpdu_user_description'],"true"); } ?> />
<?php _e( 'Please check to enable display description.', 'wp-display-users' ); ?>
                </p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"> 
                <label for="blogname">Display Website</label>
          </th>
          <td>
                <p class="description">
                <input type="checkbox" name="wpdu_user_website" id="wpdu_user_website" value="true"<?php if( !empty($_POST['wpdu_user_website']) ) { checked($_POST['wpdu_user_website'],"true"); } ?> />
<?php _e( 'Please check to enable display website.', 'wp-display-users' ); ?>
                </p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"> 
                <label for="blogname">Limit</label>
          </th>
          <td>
            <input type="text" name="wpdu_user_limit" id="wpdu_user_limit" class="regular-text" value="<?php if( !empty($_POST['wpdu_user_limit']) ) { echo $_POST['wpdu_user_limit']; } ?>" />
            <p class="description">
              <?php _e( 'Please enter here limit of display users { default display 10 users }', 'wp-display-users' ); ?>
            </p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"> 
                <label for="blogname">Select Order By</label>
          </th>
          <td>
                <select name="wpdu_order_by">
                  <option value="ID"<?php if( !empty($_POST['wpdu_order_by']) ) { selected($_POST['wpdu_order_by'],'ID'); } ?>>ID</option>
                  <option value="display_name"<?php if( !empty($_POST['wpdu_order_by']) ) { selected($_POST['wpdu_order_by'],'display_name'); } ?>>Display Name</option>
                  <option value="user_name"<?php if( !empty($_POST['wpdu_order_by']) ) { selected($_POST['wpdu_order_by'],'user_name'); } ?>>User Name</option>
                  <option value="user_login"<?php if( !empty($_POST['wpdu_order_by']) ) { selected($_POST['wpdu_order_by'],'user_login'); } ?>>User Login</option>
                  <option value="user_nicename"<?php if( !empty($_POST['wpdu_order_by']) ) { selected($_POST['wpdu_order_by'],'user_nicename'); } ?>>User Nicename</option>
                  <option value="user_nicename"<?php if( !empty($_POST['wpdu_order_by']) ) { selected($_POST['wpdu_order_by'],'user_nicename'); } ?>>User Nicename</option>
                  <option value="user_email"<?php if( !empty($_POST['wpdu_order_by']) ) { selected($_POST['wpdu_order_by'],'user_email'); } ?>>User Email</option>
                  <option value="post_count"<?php if( !empty($_POST['wpdu_order_by']) ) { selected($_POST['wpdu_order_by'],'post_count'); } ?>>Post Count</option>
                </select>
            <p class="description">
              <?php _e( 'Please select users order by.', 'wp-display-users' ); ?>
            </p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"> 
                <label for="blogname">Select Order</label>
          </th>
          <td>
               <select name="wpdu_order">
                  <option value="ASC"<?php if( !empty($_POST['wpdu_order']) ) { selected($_POST['wpdu_order'],'ASC'); } ?>>Ascending</option>
                  <option value="DESC"<?php if( !empty($_POST['wpdu_order']) ) {  selected($_POST['wpdu_order'],'DESC'); } ?>>Descending</option>
               </select>
            <p class="description">
              <?php _e( 'Please select users order.', 'wp-display-users' ); ?>
            </p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save" />
          </th>
        </tr>
      </tbody>
    </table>
  </form>
        