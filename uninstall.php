<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   communication-module
 * @author    Team Ajency <talktous@ajency.in>
 * @license   GPL-2.0+
 * @link      http://ajency.in
 * @copyright 7-24-2014 Ajency.in
 */

// If uninstall, not called from WordPress, then exit
if (!defined("WP_UNINSTALL_PLUGIN")) {
	exit;
}

//Define uninstall functionality here

 /**
 * Delete database tables
 */
function delete_plugin_data() {
    global $wpdb;  
        
	$sql = "DROP TABLE ". $wpdb->prefix."ajcm_communications";
	$wpdb->query($sql);
        
	$sql = "DROP TABLE ". $wpdb->prefix."ajcm_communication_meta";
	$wpdb->query($sql);
        
	$sql = "DROP TABLE ". $wpdb->prefix."ajcm_recipients";
	$wpdb->query($sql);
        
	$sql = "DROP TABLE ". $wpdb->prefix."ajcm_emailpreferences";
	$wpdb->query($sql);
}
delete_plugin_data();