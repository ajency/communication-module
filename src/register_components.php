<?php
/*
 * function to register communication component and communication type
 * @param string component name 
 * @param array communication type values associated to the component
 * 
 */
function register_comm_component($component_name,$component_type = array()){
    global $ajcm_components;
    
    if(!array_key_exists($component_name, $ajcm_components))
            $ajcm_components[$component_name] = array();
    
    foreach($component_type as $value){
                $ajcm_components[$component_name][]=$value;
                $ajcm_components[$component_name] = array_unique($ajcm_components[$component_name]);
    }
}
 
 


