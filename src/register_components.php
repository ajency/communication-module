<?php
/*
 * function to register communication component and communication type
 * @param string component name 
 * @param array communication type values associated to the component
 * 
 */
function register_comm_component($component_name = '',$component_type = array()){
    global $ajcm_components;
    
    $ajcm_comp = array();
    //get the hooked communication modules components/communication types and assign to global variable
    $ajcm_components = apply_filters('ajcm_communication_component_filter',$ajcm_comp);
    if($component_name != '' && !empty($component_type)){
        if(empty($ajcm_components)){
            $ajcm_components[$component_name] = array();
        }else{
            if(!array_key_exists($component_name, $ajcm_components))
                    $ajcm_components[$component_name] = array();
        }

        foreach($component_type as $key => $value){
            if(!array_key_exists($key, $ajcm_components[$component_name]))
                    $ajcm_components[$component_name][$key]=$value;
        }
    }
}

/*
 * function to get the theme defined communication components/communication type
 */
function theme_defined_components($ajcm_comp){
    $defined_comm_components = array();  // theme defined user components array  ie format array('component_name'=>array('comm_type1','comm_type1'))
    $defined_comm_components = apply_filters('add_commponents_filter',$defined_comm_components);
    
    foreach($defined_comm_components as $component => $comm_types){
            if(!array_key_exists($component, $ajcm_comp))
                $ajcm_comp[$component] = array();
            
                foreach($comm_types as $key => $value){
                 if(is_string($value)){
                    if(!array_key_exists($value, $ajcm_comp[$component]))
                       $ajcm_comp[$component][$value]=array();
                 }else{   
                    if(!array_key_exists($key, $ajcm_comp[$component]))
                       $ajcm_comp[$component][$key]=$value;
                 }
                 
                }
    }

    return $ajcm_comp;
    
}
add_filter('ajcm_communication_component_filter','theme_defined_components',10,1);

function register_doc_components($component_name = '',$component_type = array()){
    global $ajcm_document_components;
    
    $ajcm_comp = array();
    //get the hooked communication modules components/communication types and assign to global variable
    $ajcm_document_components = apply_filters('ajcm_document_component_filter',$ajcm_comp);
    if($component_name != '' && !empty($component_type)){
        if(empty($ajcm_document_components)){
            $ajcm_document_components[$component_name] = array();
        }else{
            if(!array_key_exists($component_name, $ajcm_document_components))
                    $ajcm_document_components[$component_name] = array();
        }

        foreach($component_type as $key => $value){
            if(!array_key_exists($key, $ajcm_document_components[$component_name]))
                    $ajcm_document_components[$component_name][$key]=$value;
        }
    }
}

function theme_defined_document_components($ajcm_comp){
    $defined_doc_components = array();  // theme defined user components array  ie format array('component_name'=>array('comm_type1','comm_type1'))
    $defined_doc_components = apply_filters('add_document_commponents_filter',$defined_doc_components);
    
    foreach($defined_doc_components as $component => $comm_types){
            if(!array_key_exists($component, $ajcm_comp))
                $ajcm_comp[$component] = array();
            
                foreach($comm_types as $key => $value){
                 if(is_string($value)){
                    if(!array_key_exists($value, $ajcm_comp[$component]))
                       $ajcm_comp[$component][$value]=array();
                 }else{   
                    if(!array_key_exists($key, $ajcm_comp[$component]))
                       $ajcm_comp[$component][$key]=$value;
                 }
                 
                }
    }

    return $ajcm_comp;
    
}
add_filter('ajcm_document_component_filter','theme_defined_document_components',10,1);



function register_comm_email_types($email_types){
    global $ajcm_email_types;

    $ajcm_email_types = apply_filters('ajcm_email_types_filter',$email_types);
}

function theme_defined_email_types($email_types){
    $defined_email_types = apply_filters('add_email_type_filter',$email_types);
    return $defined_email_types;
}
add_filter('ajcm_email_types_filter','theme_defined_email_types',10,1);

