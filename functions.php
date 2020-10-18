<?php
function divi__child_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'divi__child_theme_enqueue_styles' );
 
 
//you can add custom functions below this line:

/* Custom shortcodes */
include('nflrc-shortcodes.php');

/** DIVI Custom Queries for use in modules that support it. **/
function dp_dfg_custom_query_function($query, $props) {

    if (isset($props['admin_label']) && $props['admin_label'] === 'Collaborator Grid') {
        return array(
            'post_type' => 'contact',
            'posts_per_page' => -1,
            'meta_query' => array(            	
            	array('key'=>'nflrc_staff','compare'=>'!=','value'=>1),
            	array('key'=>'nflrc_role_type','compare'=>'!=','value'=>'ADVBOARD'),
            ),
        );
    } 
    else if (isset($props['admin_label']) && $props['admin_label'] === 'Advisory Board') {
        return array(
            'post_type' => 'contact',
            'posts_per_page' => -1,
            'meta_query' => array(            	
            	array('key'=>'nflrc_role_type','value'=>'ADVBOARD'),
            ),
        );
    } 
}
add_filter('dpdfg_custom_query_args', 'dp_dfg_custom_query_function', 10, 2);


/** DIVI Custom Displays for Advanced Filter Grid or other modules that support it.**/

/**
    Documentation: https://diviplugins.com/documentation/divi-filtergrid/custom-content/
    Other properties to distinguish one module instance from another:
        $props['module_id'] ==> CSS ID
        $props['admin_label'] ==> ADMIN Label
        $props['module_class'] ==> CSS Class
        module_id is what can be set for the CSS ID in the module instance settings.
*/

function dpdfg_after_read_more($content, $props) {
    if (isset($props['admin_label']) && $props['admin_label'] === 'NFLRC Items') {
        $p = get_post();

        $html = "<div class='item-content'>";
        $html .=    "<div style=''>";
        $html .=        "content";
        $html .=    "</div>"; 
        $html .=    "<div class='tags' style=''>";
        $html .=        get_the_term_list($p->ID, 'focus_area', ' ', ' ');
        $html .=    "</div>";
        $html .= "</div>"; 


        return $html;
    } 
}
add_filter('dpdfg_after_read_more', 'dpdfg_after_read_more', 10, 2);