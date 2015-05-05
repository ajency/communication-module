<?php
function ajcm_get_email_template_by_id($email_template_id, $template_args=""){
	global $wpdb;
	
	$table = $wpdb->prefix.'ajcm_templates';

	$query = "SELECT * FROM $table WHERE id=%d";

	$query_string =  $wpdb->prepare( $query, $email_template_id );

	$query_result_row =$wpdb->get_row($query_string,ARRAY_A);

	if ($query_result_row){
		$query_result_row['recipient_roles'] = maybe_unserialize( $query_result_row['recipient_roles'] );
		return $query_result_row;
	}  
	else{
		return new WP_Error( 'json_email_template_not_found', __( 'Email template not found' ));
	} 
}

function ajcm_get_templates($template_type='email'){

	global $wpdb;

	$table = $wpdb->prefix.'ajcm_templates';

	$query = "SELECT * FROM $table WHERE template_type=%s";

	$query_string =  $wpdb->prepare( $query, $template_type);

	$query_results=$wpdb->get_results($query_string,ARRAY_A);

	foreach ($query_results as $key => $query_result) {
		$created_by_user= get_user_by( 'id', $query_result['created_by'] );
		$query_results[$key]['created_by_name']  = $created_by_user->display_name ;

		$modified_by_user= get_user_by( 'id', $query_result['modified_by'] );
		$query_results[$key]['modified_by_name'] = $modified_by_user->display_name;

		$query_results[$key]['recipient_roles'] = maybe_unserialize( $query_result['recipient_roles'] );
	}


	return array('data' => $query_results);
}

function ajcm_create_email_template($args){
	global $wpdb;

	// if (!isset($args['component'])||!isset($args['communication_type'])||!isset($args['email_type'])||!isset($args['vendor_template_id']) ||!isset($args['recipient_roles'])) {
	// 	return new WP_Error( 'json_missing_arguments', __( 'Specify all mandatory arguments' ));
	// }

	// if (sizeof($args['recipient_roles'])<1) {
	// 	return new WP_Error( 'json_missing_arguments', __( 'Specify email recepients' ));
	// }

	if (isset($args['status']) && $args['status'] === true ) {
		$args['status'] = 'active';
	}else{
		$args['status'] = 'archived';
	}

	$table = $wpdb->prefix.'ajcm_templates';

	//@todo set to whichever timezone later
	date_default_timezone_set('Asia/Kolkata');
	
	$defaults = array(
		'template_type' => 'email',
		'component'           => '',    
		'communication_type'  => '',                  
		'email_type'             => '', 
		'vendor_template_src'  => 'mandrill',
		'vendor_template_id'       => '',
		'created_by'             => get_current_user_id(),
		'modified_by'             => get_current_user_id(),
		'sender_customizable'    => 0,
		'created_at'    => date('Y-m-d h:i:s a', time()),
		'status' 				=> 'active',
		);
	$params = wp_parse_args( $args, $defaults );
	
	extract( $params, EXTR_SKIP );

	$insert_query = $wpdb->insert( $table, array(
		'template_type' => $template_type,
		'component' => $component,
		'communication_type' => $communication_type,
		'email_type'           => $email_type,
		'vendor_template_src'  => $vendor_template_src,
		'vendor_template_id'   => $vendor_template_id,
		'created_by'          =>$created_by,
		'modified_by'           =>$modified_by,
		'created_at'           =>$created_at,
		'recipient_roles'           =>maybe_serialize($recipient_roles),
		'sender_customizable'           =>$sender_customizable,
		'status'         =>$status
		));

	if ( false === $insert_query )
		return new WP_Error('template_creation_failed', __('Failed to create template') );

	$email_template_id = $wpdb->insert_id;

	$template_args = array('status' => $status );
	$email_template_data = ajcm_get_email_template_by_id($email_template_id,$template_args);
	
	if(is_wp_error($email_template_data)){
		return $email_template_data;
	}
	$email_template_data['recipient_roles'] = maybe_unserialize( $email_template_data['recipient_roles'] );

	$response['code'] = 'template_created';
	$response['message'] = __('Template created');
	$response['data'] = $email_template_data;

	return $response;

}

function ajcm_update_email_template($args){
	global $wpdb;

	if (!isset($args['component'])||!isset($args['communication_type'])||!isset($args['email_type'])||!isset($args['vendor_template_id']) ||!isset($args['recipient_roles'])) {
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

	$table = $wpdb->prefix.'ajcm_templates';

	$defaults = array(
		'modified_by'             => get_current_user_id(),
		'sender_customizable'    => 0,
		'vendor_template_src'  => 'mandrill',
		'status' 				=> 'active',
		);
	$params = wp_parse_args( $args, $defaults );
	
	extract( $params, EXTR_SKIP );

	$update_query = $wpdb->update( 
		$table, 
		array(
		'component' => $component,
		'communication_type' => $communication_type,
		'email_type'           => $email_type,
		'vendor_template_src'  => $vendor_template_src,
		'vendor_template_id'           => $vendor_template_id,
		'created_by'          =>$created_by,
		'modified_by'           =>$modified_by,
		'recipient_roles'           =>maybe_serialize($recipient_roles),
		'sender_customizable'           =>$sender_customizable,
		'status'         =>$status
		), 
		array( 'id' => $id )
	);	

	if ( false === $update_query )
		return new WP_Error('emailtemplate_update_failed', __('Failed to update email template') );

	$template_args = array('status' => $status );
	$email_template_data = ajcm_get_email_template_by_id($id,$template_args);
	
	if(is_wp_error($email_template_data)){
		return $email_template_data;
	}
	$email_template_data['recipient_roles'] = maybe_unserialize( $email_template_data['recipient_roles'] );

	$response['code'] = 'email_template_updated';
	$response['message'] = __('Email template updated');
	$response['data'] = $email_template_data;

	return $response;
}


function ajcm_get_mandrill_templates($label='',$mail_api = 'mandrill'){

	switch ($mail_api) {
		case "mandrill":
        	$ajcm_plugin_options = get_option('ajcm_plugin_options'); // get the plugin options
        	$all_mandrill_templates = array();

        	if(isset($ajcm_plugin_options['ajcm_mandrill_key']) && $ajcm_plugin_options['ajcm_mandrill_key'] != ''){
                //create a an instance of Mandrill and pass the api key
        		$mandrill = new AJCOMM_Mandrill($ajcm_plugin_options['ajcm_mandrill_key']);
        		$url = '/templates/list';

        		if ($label!=='') {
        			$params = array('label' => $label );
        			$responseTemplates  =  $mandrill->call($url,$params);
        		}else{
        			$responseTemplates  =  $mandrill->call($url);
        		}
        		if (isset($responseTemplates['status']) && $responseTemplates['status']!=="error") {
	        		return new WP_Error( $responseTemplates['name'], __( $responseTemplates['message'] ));
        		}
        		else{
        			
        			foreach ($responseTemplates as $key => $responseTemplate) {
	        			$all_mandrill_templates[] =  array('slug' => $responseTemplate['slug'], 'name'=>$responseTemplate['name'], 'created_at'=>$responseTemplate['created_at'],'updated_at'=>$responseTemplate['updated_at'] );
	        		}

	        		return array('data' =>$all_mandrill_templates );
        		}

        		
        	}
        	break;
		default:
		    break;
	}
}


if(!function_exists('_log')){
    function _log( $message ) {
        if( WP_DEBUG === true ){
            if( is_array( $message ) || is_object( $message ) ){
                error_log( print_r( $message, true ) );
            } else {
                error_log( $message );
            }
        }
    }
}

