<?php
/*
Plugin Name: Mingle Donation Button
Plugin URI: http://blairwilliams.com/mingle
Description: Enables your mingle users to add a paypal donation button to their profile page...
Version: 0.0.02
Author: Blair Williams
Author URI: http://blairwilliams.com
Text Domain: mingle
Copyright: 2009-2010, Blair Williams

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if(!function_exists('is_plugin_active'))
  require_once(ABSPATH . '/wp-admin/includes/plugin.php');
  
if(is_plugin_active('mingle/mingle.php'))
{
  class MnglDonateButton
  {
    function MnglDonateButton()
    {
      add_action('mngl-profile-display',  array( &$this, 'display_button' ));
      add_action('mngl-edit-user-fields', array( &$this, 'display_profile_fields' ));
      add_action('mngl-profile-validate', array( &$this, 'validate_profile_fields' ));
      add_action('mngl-profile-update',   array( &$this, 'process_profile_fields' ));
    }
    
    function display_button($user_id)
    {  
      $paypal_email = get_usermeta($user_id, 'mngldonatebutton_paypal_email');
      
      if( isset($paypal_email) and
          $paypal_email and
          !empty($paypal_email) )
      {
        $user =& MnglUser::get_stored_profile_by_id($user_id);

        $paypal_email = get_usermeta($user_id,'mngldonatebutton_paypal_email');

        ?>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
          <input type="hidden" name="cmd" value="_donations">
          <input type="hidden" name="business" value="<?php echo $paypal_email; ?>">
          <input type="hidden" name="lc" value="US">
          <input type="hidden" name="item_name" value="<?php echo $user->full_name; ?>">
          <input type="hidden" name="currency_code" value="USD">
          <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest">
          <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" style="border: 0; background: transparent; padding: 0; margin: 0;" name="submit" alt="PayPal - The safer, easier way to pay online!">
          <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
        <?php
      }
    }
    
    function display_profile_fields()
    {
      global $mngl_user;
      
      if(isset($_POST['mngldonatebutton_paypal_email']) and !empty($_POST['mngldonatebutton_paypal_email']))
        $paypal_email = $_POST['mngldonatebutton_paypal_email'];
      else
        $paypal_email = get_usermeta($mngl_user->id, 'mngldonatebutton_paypal_email');

      ?>
      <tr>
        <td valign="top"><?php _e('PayPal Email'); ?>:</td>
        <td valign="top"><input type="text" name="mngldonatebutton_paypal_email" id="mngldonatebutton_paypal_email" value="<?php echo $paypal_email; ?>" class="mngl-profile-edit-field" /></td>
      </tr>
      <?php
    }
    
    function validate_profile_fields($errors)
    {
      if( isset($_POST['mngldonatebutton_paypal_email']) and
          !empty($_POST['mngldonatebutton_paypal_email']) and
          !is_email($_POST['mngldonatebutton_paypal_email']) )
        $errors[] = __('PayPal Email must be a real and properly formatted email address','mingle');
      
      return $errors;
    }
    
    function process_profile_fields($user_id)
    {
      if(isset($_POST['mngldonatebutton_paypal_email']))
        update_usermeta($user_id, 'mngldonatebutton_paypal_email', $_POST['mngldonatebutton_paypal_email']);
    }
  }
  
  new MnglDonateButton();
}
?>
