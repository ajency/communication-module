<?php
/*
 * function to register communication component and communication type
 * @param string component name 
 * @param array communication type values associated to the component
 * 
 */
function register_comm_component($component_name = '',$component_type = array()){
    global $ajcm_components;
    
    //get the hooked communication modules components/communication types and assign to global variable
    $ajcm_components = apply_filters('ajcm_communication_component_filter');
    
    if($component_name != '' && !empty($component_type)){
        if(!array_key_exists($component_name, $ajcm_components))
                $ajcm_components[$component_name] = array();

        foreach($component_type as $value){
                    $ajcm_components[$component_name][]=$value;
                    $ajcm_components[$component_name] = array_unique($ajcm_components[$component_name]);
        }
    }
}

/*
 * function to get the theme defined communication components/communication type
 */
function theme_defined_components(){
    global $ajcm_components; 
    $defined_comm_components = array();  // theme defined user components array  ie format array('component_name'=>array('comm_type1','comm_type1'))
    $defined_comm_components = apply_filters('add_commponents_filter',$defined_comm_components);
    
    foreach($defined_comm_components as $component => $comm_types){
            if(!array_key_exists($component, $comm_types))
                $ajcm_components[$component] = array();
            
                foreach($comm_types as $value){
                $ajcm_components[$component][]=$value;
                $ajcm_components[$component] = array_unique($ajcm_components[$component]);
                }
    }
    
    return $ajcm_components;
    
}
add_filter('ajcm_communication_component_filter','theme_defined_components');