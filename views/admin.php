<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   communication-module
 * @author    Team Ajency <talktous@ajency.in>
 * @license   GPL-2.0+
 * @link      http://ajency.in
 * @copyright 7-24-2014 Ajency.in
 */
?>
<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
        <p>Plugin Settings</p>
        <?php 
        global $ajcm_components;
        //var_dump($ajcm_components);
       
         if (isset($_POST['submit'])){
             $optionsarray= array();

             $optionsarray['ajcm_mandrill_key'] = $_POST['ajcm_mandrill_key'];
             
             update_option('ajcm_plugin_options', $optionsarray);
         }  
         
          $ajcm_plugin_options= get_option('ajcm_plugin_options');
        ?>
        
        <form method="post">
            <table>
            <tr>
            <td>
            <label>Mandrill Api Key: 
            </label></td>	
            <td>
            <input type="text"
            name="ajcm_mandrill_key"
            size="30"
            value="<?php echo $ajcm_plugin_options['ajcm_mandrill_key'] ?>" />
            </td>
            </tr>
            </table>
            <input type="submit"
            name="submit"
            value="Save Changes" /> 
        </form>

</div>
