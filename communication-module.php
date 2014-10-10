<?php
/**
 * Communication Module
 *
 * A simple Communication module for wordpress
 *
 * @package   communication-module
 * @author    Team Ajency <talktous@ajency.in>
 * @license   GPL-2.0+
 * @link      http://ajency.in
 * @copyright 7-24-2014 Ajency.in
 *
 * @wordpress-plugin
 * Plugin Name: Communication Module
 * Plugin URI:  http://ajency.in
 * Description: A simple Communication module for wordpress
 * Version:     0.1.0
 * Author:      Team Ajency
 * Author URI:  http://ajency.in
 * Text Domain: communication-module-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if (!defined("WPINC")) {
	die;
}

// include the register components functions file
require_once( plugin_dir_path( __FILE__ ) . '/src/register_components.php');

// include the plugin api file
require_once( plugin_dir_path( __FILE__ ) . '/src/api.php');


require_once(plugin_dir_path(__FILE__) . "CommunicationModule.php");

// include the Mandrill API class file to send emails through Mandrill
require_once( plugin_dir_path( __FILE__ ) . '/src/Mandrill.php');

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook(__FILE__, array("CommunicationModule", "activate"));
register_deactivation_hook(__FILE__, array("CommunicationModule", "deactivate"));

//$aj_comm = CommunicationModule::get_instance();

function aj_communication() {
	return CommunicationModule::get_instance();
}

// add the communication module instance to globals
$GLOBALS['aj_comm'] = aj_communication();

function isMultiArray($a){
    foreach($a as $v) if(is_array($v)) return TRUE;
    return FALSE;
}
