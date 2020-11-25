<?php
function divi__child_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'divi__child_theme_enqueue_styles' );
 

// add_action( 'et_before_main_content', function () {
//     echo '<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">';
//     if ( function_exists('bcn_display') ) {
//         bcn_display();
//     }
//     echo '</div>';
// } );


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
            	array('key'=>'nflrc_role_type','compare'=>'==','value'=>'COLLAB'),
            ),
        );
    } 
    else if (isset($props['admin_label']) && $props['admin_label'] === 'Advisory Board') {
        return array(
            'post_type' => 'contact',
            'category_name' => -1,
            'meta_query' => array(            	
            	array('key'=>'nflrc_role_type','value'=>'ADVBOARD'),
            ),
        );
    }
    else if (isset($props['admin_label']) && $props['admin_label'] === 'NFLRC Items') {
        // var_dump(get_post());
        $page_cat = $props['module_id'];  // Use the string in CSS ID for category filter
        var_dump($page_cat);

        return array(
            'post_type'     => array('project','publication','prodev','contact','story'),
            'category_name' => $page_cat,
            'meta_key'      => 'menu_order',
            'orderby'       => 'meta_value_num',
            'order'         => 'DESC',
            'posts_per_page' => 6,            
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
        $p_type = $p->post_type;
        $p_title = wp_trim_words($p->post_title, 8, ' ...');

        // $html = "<div class='item-content-wrapper'><div class='item-content'>";
        // $html .=    "<div>";
        // $html .=        "<h4> {$p_title} </h4>";  
        // $html .=    "</div>"; 
        // $html .=    "<div>";
        // $html .=        wp_trim_words(get_the_excerpt($p->ID), 20, ' ...');  
        // $html .=    "</div>"; 
        $html .=    "<div class='tags'>";
        $html .=        "<p>" . get_the_term_list($p->ID, 'focus_area', ' ', ' ') . "</p>";
        $html .=        "<p>{$p_type}</p>";
        $html .=    "</div>";
        // $html .= "</div></div>"; 


        return $html;
    } 
}
add_filter('dpdfg_after_read_more', 'dpdfg_after_read_more', 10, 2);