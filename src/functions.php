<?php

function ajcm_get_email_templates(){


	global $wpdb;

	$table = $wpdb->prefix.'ajcm_emailtemplates';

	$query = "SELECT * FROM $table WHERE status=%s";

	$status = 'active';

	$query_string =  $wpdb->prepare( $query, $status);

	$query_results=$wpdb->get_results($query_string,ARRAY_A);

	if ($query_results){
		return $query_results;
	}  
	else{
		return new WP_Error( 'json_email_templates_not_found', __( 'Email templates not found' ));
	} 
}