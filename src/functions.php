<?php
function ajcm_get_email_template_by_id($email_template_id, $template_args=""){
	global $wpdb;
	
	$table = $wpdb->prefix.'ajcm_emailtemplates';

	$query = "SELECT * FROM $table WHERE id=%d";

	$query_string =  $wpdb->prepare( $query, $email_template_id );

	$query_result_row =$wpdb->get_row($query_string,ARRAY_A);

	if ($query_result_row){
		return $query_result_row;
	}  
	else{
		return new WP_Error( 'json_email_template_not_found', __( 'Email template not found' ));
	} 
}

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
		return new WP_Error( 'json_not_found', __( 'Email templates not found' ));
	} 
}

function ajcm_create_email_template($args){
	global $wpdb;

	if (!isset($args['component'])||!isset($args['communication_type'])||!isset($args['email_type'])||!isset($args['mandrill_template']) ||!isset($args['recipient_roles'])) {
		return new WP_Error( 'json_missing_arguments', __( 'Specify all mandatory arguments' ));
	}

	if (sizeof($args['recipient_roles'])<1) {
		return new WP_Error( 'json_missing_arguments', __( 'Specify email recepients' ));
	}

	if (isset($args['status']) && $args['status'] === true ) {
		$args['status'] = 'active';
	}else{
		$args['status'] = 'archived';
	}

	$table = $wpdb->prefix.'ajcm_emailtemplates';

	$defaults = array(
		'component'           => '',    
		'communication_type'  => '',                  
		'email_type'             => '', 
		'mandrill_template'       => '',
		'created_by'             => get_current_user_id(),
		'modified_by'             => get_current_user_id(),
		'sender_customizable'    => 0,
		'status' 				=> 'active',
		);
	$params = wp_parse_args( $args, $defaults );
	
	extract( $params, EXTR_SKIP );

	$insert_query = $wpdb->insert( $table, array(
		'component' => $component,
		'communication_type' => $communication_type,
		'email_type'           => $email_type,
		'mandrill_template'           => $mandrill_template,
		'created_by'          =>$created_by,
		'modified_by'           =>$modified_by,
		'recipient_roles'           =>maybe_serialize($recipient_roles),
		'sender_customizable'           =>$sender_customizable,
		'status'         =>$status
		));

	if ( false === $insert_query )
		return new WP_Error('emailtemplate_creation_failed', __('Failed to create email template') );

	$email_template_id = $wpdb->insert_id;

	$template_args = array('status' => $status );
	$email_template_data = ajcm_get_email_template_by_id($email_template_id,$template_args);
	
	if(is_wp_error($email_template_data)){
		return $email_template_data;
	}
	$email_template_data['recipient_roles'] = maybe_unserialize( $email_template_data['recipient_roles'] );

	$response['code'] = 'email_template_created';
	$response['message'] = __('Email template created');
	$response['data'] = $email_template_data;

	return $response;

}

