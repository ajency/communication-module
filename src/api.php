<?php
/*
 * Api configuration and methods of the plugin
 * 
 */

include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
if(is_plugin_active('json-rest-api/plugin.php')){
    
    /*
     * function to configure the plugin api routes
     */
    function communicationmodule_plugin_api_init($server) {
        global $communicationmodule_plugin_api;

        $communicationmodule_plugin_api = new CommunicationModuleAPI($server);
        add_filter( 'json_endpoints', array( $communicationmodule_plugin_api, 'register_routes' ) );
    }
    add_action( 'wp_json_server_before_serve', 'communicationmodule_plugin_api_init',10,1 );

    class CommunicationModuleAPI {

        /**
         * Server object
         *
         * @var WP_JSON_ResponseHandler
         */
        protected $server;

        /**
         * Constructor
         *
         * @param WP_JSON_ResponseHandler $server Server object
         */
        public function __construct(WP_JSON_ResponseHandler $server) {
                $this->server = $server;
        }

        /*Register Routes*/
        public function register_routes( $routes ) {
             
             $routes['/ajcm/components'] = array(
                array( array( $this, 'get_components'), WP_JSON_Server::READABLE ),
                );
             $routes['/ajcm/components/(?P<component_name>\w+)'] = array(
                array( array( $this, 'get_component'), WP_JSON_Server::READABLE ),
                );
             $routes['/ajcm/emailpreferences/(?P<user_id>\d+)'] = array(
                array( array( $this, 'user_emailpreferences'), WP_JSON_Server::READABLE ),
                );
             $routes['/ajcm/emailpreferences/(?P<user_id>\d+)/(?P<communication_type>\w+)'] = array(
                array( array( $this, 'user_emailpreference'), WP_JSON_Server::READABLE ),
                ); 
             $routes['/ajcm/emailpreferences/(?P<user_id>\d+)/(?P<component>\w+)/(?P<communication_type>\w+)'] = array(
                array( array( $this, 'update_user_emailpreference'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
                ); 
             $routes['/ajcm/mandrill/templatepreview'] = array(
                array( array( $this, 'get_template_preview'), WP_JSON_Server::CREATABLE  | WP_JSON_Server::ACCEPT_JSON ),
                );              
            return $routes;
        }
        
        public function get_components(){
            global $ajcm_components;
            
            if(is_null($ajcm_components)){
                wp_send_json_error($ajcm_components);
            }else{
               wp_send_json(array('success'=>true,'data'=>$ajcm_components));
            }
            

        }
        
        public function get_component($component_name){
            global $ajcm_components;
            
            if(!array_key_exists($component_name, $ajcm_components)){
                wp_send_json_error(array());
            }else{
                wp_send_json(array('success'=>true,'data'=>$ajcm_components[$component_name]));
            }
        }
        
        public function user_emailpreferences($user_id){    
            global $aj_comm;
            
            $user_id = intval($user_id);
            $response = $aj_comm->get_user_preferences($user_id);
            if(empty($response)){
             wp_send_json_error($response);
            }else{
             wp_send_json(array('success'=>true,'data'=>$response));
            }
           
        }
        
        public function user_emailpreference($user_id,$communication_type){
            global $aj_comm;
            $user_id = intval($user_id);
            $response = $aj_comm->get_user_preferences($user_id,$communication_type);
            if(empty($response)){
                wp_send_json_error($response);
            }else{
                if($response[$communication_type] == 'yes' ){
                    $ret = 1;
                }else{
                    $ret = 0;
                }
                wp_send_json(array('success'=>true,'data'=>$ret));
            } 
        }
        
        public function update_user_emailpreference($user_id,$component,$communication_type,$data){
            global $aj_comm;
            // check if component,communication type is registered
            if(! $aj_comm->is_registered_component_type($component,$communication_type) ){
                $response = array('data' => array('msg'=>'Communication type not registered.'));
                wp_send_json_error($response);
            }
            
            if(! $aj_comm->is_preference_editable($component,$communication_type) ){
                $response = array('data' => array('msg'=>'Preference not editable.'));
                wp_send_json_error($response);
            }
            
            $preference = (bool) $data['preference'];
            $preference = ($preference == true) ? 'yes':'no';
            $resp = $aj_comm->update_user_email_preference($preference,$user_id,$communication_type);
            wp_send_json(array('success'=>true,'data'=>$resp));
        }
        
        public function get_template_preview($data){
            
            wp_send_json($data);
        }
            
    }

}
