<?php
function divi__child_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'divi__child_theme_enqueue_styles' );
 
 
//you can add custom functions below this line:
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