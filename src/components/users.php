<?php
/*
 * functions to get communication type variables for sending emails
 */

/*
 * function to get the forgot password email dynamic variables 
 * @param array $recipients_email a multidemensional array of recipient data
 * @param $comm_data data about the communication to be processed (id,component,communication_type)
 * 
 * @return array $template_data
 * 
 */
function getvars_forgot_password($recipients_email,$comm_data){
    global $aj_comm;
    
    $template_data = array();
    
    $template_data['name'] = 'forgot-password'; // [slug] name or slug of a template that exists in the user's account
    $homeurl = network_home_url( '/' );
    $recipient = $recipients_email[0];
    $recipient_user = get_user_by( 'id', $recipient->user_id );
    $userlogin = $recipient_user->user_login;
    $key = $aj_comm->get_communication_meta($comm_data['id'],'reset_key');
    $reseturl = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($userlogin), 'login');
    $template_data['dynamic_content'] = array();
    $template_data['dynamic_content'][] = array('name' =>'homeurl','content' =>$homeurl);
    $template_data['dynamic_content'][] = array('name' =>'userlogin','content' =>$userlogin);
    $template_data['dynamic_content'][] = array('name' =>'reseturl','content' => $reseturl);

    $template_data['global_merge_vars'] = array();
    $template_data['global_merge_vars'][] = array('name' => 'FNAME','content' => $recipient_user->display_name);
       // The blogname option is escaped with esc_html on the way into the database in sanitize_option
       // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $title = sprintf( __('[%s] Password Reset'), $blogname );


    $title = apply_filters( 'retrieve_password_title', $title );
    $template_data['subject'] = $title;   
    return $template_data;
}

function getvars_registration(){
    
}

function getvars_activation(){
    
}

