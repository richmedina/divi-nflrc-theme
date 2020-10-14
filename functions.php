<?php
function divi__child_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'divi__child_theme_enqueue_styles' );
 
 
//you can add custom functions below this line:

/* Custom shortcodes */
include('nflrc-shortcodes.php');


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


function dpdfg_after_read_more($content, $props) {
    if (isset($props['admin_label']) && $props['admin_label'] === 'NFLRC Items') {
        $p = get_post();

        // $single = has_post_thumbnail() ? "" : ".single";
        // $resource_type_blk = display_as_resource_block($d['resource_type'], $d['access_link']);
        // $description = wp_trim_words($content, 15, ' ...');

        $html = $props;     
        // $html = "<div class='card-wrap-row{$single}'>";
        // $html .=    "<div>{$d['thumb']}</div>";  //Thumbnail column
        // $html .=    "<div class='card'>";
        // $html .=        "<header class='card-header'>";
        // $html .=            "<h4 class='card-title'><a href='{$d['link']}'>{$d['title']}</a></h4>";
        // $html .=            $resource_type_blk;
        // $html .=            $d['people'];
        // $html .=        "</header>";
        
        // $html .=        "<div class='card-body'><div class='date_str'>{$d['start_date']}</div>{$description}</div>";
        
        // $html .=        "<div class='card-footer'>";
        // $html .=            "<div class='tag-series'>{$d['series']}</div>";
        // $html .=            "<div class='tags'>{$d['tags']}</div>";
        // $html .=            "<div class='mod-date'><time>Updated {$d['mod_date']}</time></div>";
        // $html .=        "</div>"; //END footer
        // $html .=    "</div>"; //END card    
        // $html .= "</div>"; //END grid row
        $html .= "<div class='tags'>";
        // $html .= get_the_term_list( $p->ID, 'category', ' ', ' ');
        $html .= get_the_term_list( $p->ID, 'focus_area', ' ', ' ');
        $html .= "</div>";

        return $html;
    } 
}
add_filter('dpdfg_after_read_more', 'dpdfg_after_read_more', 10, 2);