<?php 


function read_nflrc_fields($post) {
	global $post;
	$post_type = $post->post_type;
	$fields = array(
		    'title' 	=> $post->post_title,
		    'excerpt' 	=> $post->post_excerpt,
		    'post_type' => $post_type,
		    'icon' 		=> get_the_post_thumbnail(),
		    'link'		=> get_the_permalink(),
		    'content'   => $post->post_content,
		);

	if($post_type === 'project') {
		$fields['cycle'] = $post->grant_cycle;
		$fields['language'] = $post->language;
		$fields['director'] = $post->director;
		$fields['project_number'] = $post->project_number;

	} else if($post_type === 'publication') {
		$fields['language'] = $post->language;
		$fields['author'] = $post->author;
		$fields['oer'] = $post->is_oer;
		$fields['ext_url'] = $post->external_url;
		$fields['url'] = $post->url;
		$fields['oclc_url'] = $post->oclc_url;
		$fields['order_url'] = $post->order_from;
		$fields['category'] = $post->category;
		$fields['apa_citation'] = $post->apa_citation;

	} else if($post_type === 'prodev') {
		$fields['language'] = $post->language;
		$fields['event_date'] = $post->event_date;
		$fields['facilitator'] = $post->facilitator;
		$fields['director'] = $post->director;
		$fields['event_type'] = $post->event_type;
		$fields['url'] = $post->url;

	} else if($post_type === 'contact') {
		$fields['nflrc_role'] = $post->nflrc_role;
		$fields['nflrc_staff'] = $post->nflrc_staff;
		$fields['nflrc_role_type'] = $post->nflrc_role_type;

	} else if($post_type === 'story') {

	}
	return $fields;	
}

// function get_csv_terms($fname) {
// 	$file = fopen($fname, "r");
// 	$data = array();
// 	// while(! feof($file)) {
// 	//   array_push($data, fgetcsv($file));
// 	// }

// 	// fclose($file);
// 	return $data;
// }


add_shortcode('featured_img', 'featured_img_func');
function featured_img_func($atts, $content = null) {
    return get_the_post_thumbnail();
}

add_shortcode('today', 'today_func');
function today_func($atts, $content = null) {
	$d = date('d');
    return $d;
}

add_shortcode('post_excerpt', 'post_excerpt_func');
function post_excerpt_func($atts, $content = null) {
    $a = shortcode_atts( array(
        'id' => '',
    ), $atts );
    global $post;
    $post = get_post($a['id']);
    return get_the_excerpt();;
}

add_shortcode('import_csv_tags_form', 'import_csv_tags_form_func');
function import_csv_tags_form_func($atts, $content = null) {
  if (isset($_POST['submit'])) {
    $csv_file = $_FILES['csv_file'];
    $csv_to_array = array_map('str_getcsv', file($csv_file['tmp_name']));
		
    foreach ($csv_to_array as $key => $value) {
    	wp_insert_term($value[0], "post_tag");
    }
  } else {
  	echo '<h2>Import terms from django site:</h2>';
    echo '<form action="" method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="csv_file">';
    echo '<input type="submit" name="submit" value="submit">';
    echo '</form>';
  }
}

add_shortcode('import_json_data_migration_form', 'import_json_data_migration_form_func');
function import_json_data_migration_form_func($atts, $content = null) {
  if (isset($_POST['submit'])) {
  	$count = 0;
  	$output = "";
    $json_form_file = $_FILES['json_file'];
    $json_obj = file_get_contents($json_form_file['tmp_name']);
    $json_data = json_decode($json_obj,true);
    	
	foreach ($json_data as $key1 => $value) {
	    // [POST TYPE, POSTGRES_PK, DESCRIPTION, THUMBNAIL_DESC, OTHER]

		// Get the post based on type and postgres_pk field
		$args = array(
			'numberposts' 		=> 1,
			'meta_key'       	=> 'postgres_pk',
			'meta_value'		=> $value[1],
		    'post_type'      	=> $value[0],
		);
		$posts = new WP_Query($args);

		if ( $posts->have_posts() ) {
			$count += 1;
			global $post;
		    $posts->the_post();
			$my_post = array(
	      		'ID'          => $post->ID,
	      		'post_content'=> $value[2],
	      		'post_excerpt'=> $value[3],
	      	);
	      	// <== SAFETY SWITCH ON ==> wp_update_post( $my_post );
	      	$output .= "<div>{$key1} ==> {$value[0]} | {$value[1]}</div>";     	
		}
		wp_reset_postdata();

	}
	$output .= "<h2>{$count}</h2>";
	return $output;
    
  } else {
  	echo '<h2>Import json data from Django site (test function):</h2>';
    echo '<form action="" method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="json_file">';
    echo '<input type="submit" name="submit" value="submit">';
    echo '</form>';
  }
}

add_shortcode('import_json_ordering_form', 'import_json_ordering_form_func');
function import_json_ordering_form_func($atts, $content = null) {
  if (isset($_POST['submit'])) {
  	$count = 0;
  	$output = "";
    $json_form_file = $_FILES['json_file'];
    $json_obj = file_get_contents($json_form_file['tmp_name']);
    $json_data = json_decode($json_obj,true);
    // var_dump($json_data);

	foreach ($json_data as $key1 => $value) {
	    // [POST TYPE, POSTGRES_PK, IS FEATURED, FEATURED RANK, LISTING RANK]

		// Get the post based on type and postgres_pk field
		$args = array(
			'numberposts' 		=> 1,
			'meta_key'       	=> 'postgres_pk',
			'meta_value'		=> $value[1],
		    'post_type'      	=> $value[0],
		);
		$posts = new WP_Query($args);

		if ( $posts->have_posts() ) {
			$count += 1;
			global $post;
		    $posts->the_post();

		    // wp_add_post_tags($post->ID, "featured");
			$my_post = array(
	      		'ID'          => $post->ID,
	      		'menu_order'  => $value[3],
	      	);
	      	// <== SAFETY SWITCH ON ==> 
	      	// wp_update_post( $my_post );
	      	$output .= "<div>{$key1} ==> {$value[0]} | {$value[1]} | {$value[2]}</div>";     	
		}
		wp_reset_postdata();

	}
	$output .= "<h2>{$count}</h2>";
	return $output;
    
  } else {
  	echo '<h2>Import json data from Django site (test function):</h2>';
    echo '<form action="" method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="json_file">';
    echo '<input type="submit" name="submit" value="submit">';
    echo '</form>';
  }
}


add_shortcode('import_csv_tag_mapping_form', 'import_csv_tag_mapping_form_func');
function import_csv_tag_mapping_form_func($atts, $content = null) {
  if (isset($_POST['submit'])) {
    $csv_file = $_FILES['csv_file'];
    $csv_to_array = array_map('str_getcsv', file($csv_file['tmp_name']));
	$output = "";
	$count = 0;
    foreach ($csv_to_array as $key => $value) {
    	// var_dump($value);
    	//  [0]=> string(7) "project" [1]=> string(2) "48" [2]=> string(10) "assessment" 
		$args = array(
			'numberposts' 		=> 1,
			'meta_key'       	=> 'postgres_pk',
			'meta_value'		=> $value[1],
		    'post_type'      	=> $value[0]
		);
		$posts = new WP_Query($args);

		if ( $posts->have_posts() ) {
			$count += 1;
			global $post;
		    $posts->the_post();
		    wp_add_post_tags($post->ID, $value[2]);
		    $output .= "<div>{$post->ID} {$value[0]} {$value[1]} {$value[2]} {$post->post_title}</div>";
		}
		wp_reset_postdata();
	}
	$output .= $count;
	return $output;
  } else {
  	echo '<h2>Import object/term relations from django site:</h2>';
    echo '<form action="" method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="csv_file">';
    echo '<input type="submit" name="submit" value="submit">';
    echo '</form>';
  }
}

add_shortcode('import_csv_tag_oer_form', 'import_csv_tag_oer_form_func');
function import_csv_tag_oer_form_func($atts, $content = null) {
  if (isset($_POST['submit'])) {
    $csv_file = $_FILES['csv_file'];
    $csv_to_array = array_map('str_getcsv', file($csv_file['tmp_name']));
	$output = "";
	$count = 0;
    foreach ($csv_to_array as $key => $value) {
    	// var_dump($value);
    	//  [0]=> string(7) "project" [1]=> string(2) "48" [2]=> string(10) "assessment" 
		$args = array(
			'numberposts' 		=> 1,
			'meta_key'       	=> 'postgres_pk',
			'meta_value'		=> $value[0],
		    'post_type'      	=> 'publication'
		);
		$posts = new WP_Query($args);

		if ( $posts->have_posts() ) {
			$count += 1;
			global $post;
		    $posts->the_post();
		    // wp_add_post_tags($post->ID, 'OER' );
		    $output .= "<div>{$post->ID} {$post->post_name}</div>";
		}
		wp_reset_postdata();
	}
	$output .= $count;
	return $output;
  } else {
  	echo '<h2>Import items to tag as OER from django site:</h2>';
    echo '<form action="" method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="csv_file">';
    echo '<input type="submit" name="submit" value="submit">';
    echo '</form>';
  }
}

// [nflrc_feature_list]
/* Displays all post types flagged as featured ordered by featured rank.
*/
add_shortcode( 'nflrc_feature_list', 'nflrc_feature_list_func' );
function nflrc_feature_list_func($atts, $content = null) {
	$output = "";
	$args = array(
	    // 'numberposts'   	=> -1,
	    'post_type'      	=> array( 'project', 'prodev', 'publication', 'story' ),
	    'meta_query'     	=> array('key'=>'featured','compare'=>'=','value'=>'t'),
	    'meta_key'       	=> 'featured_rank',
	    'orderby'			=> 'meta_value_num',
	    'order'   			=> 'ASC',
	    'posts_per_page' 	=> 70,

	);
	$posts = new WP_Query($args);

	if ( $posts->have_posts() ) {
	    $output = "";
	    while ( $posts->have_posts() ) {
	        $posts->the_post();
	        $title = get_the_title();
	        $ptype = $post->post_type;
	        $featured = get_field('featured', $post->ID);
	        $featured_rnk = get_field('featured_rank', $post->ID);
	        $cycle = get_field('grant_cycle', $post->ID);
	        $output .= "<div>{$title}</div>";
	    }	    
	} else {
	    $output .= "<div>No matching posts.</div>";
	}
	/* Restore original Post Data */
	// wp_reset_postdata();
	return $output;
}

//[nflrc_project_block]
/* Displays all post types flagged as featured ordered by featured rank.

Attributes: 
post_slug - slug of item to display in the block
cls_str - horizontal (default) or vertical layout
Example: [nflrc_post_block post_slug="issues-in-placement" cls_str="vertical"]
*/
add_shortcode( 'nflrc_post_block', 'nflrc_post_block_func' );
function nflrc_post_block_func($atts, $content = null) {
	$a = shortcode_atts( array(
		'post_slug' => '',
		'cls_str' => 'horizontal',
	), $atts );
	
	$slug = sanitize_text_field($a['post_slug']);
	$args = array(
		'numberposts' 		=> 1,
		'name'				=> $slug,
	    'post_type'      	=> array('project', 'prodev', 'publication', 'contact', 'story'),
	);
	$posts = new WP_Query($args);
	$output = '';
	if ( $posts->have_posts() ) {
	    global $post;
	    while ( $posts->have_posts() ) {
	    	$posts->the_post();
	    	$data = read_nflrc_fields($post);
	    	$output .= "<article class='grid_block {$a['cls_str']}'>";
	    	$output .= "<div><a href='{$data['link']}'>{$data['icon']}</a></div>";
	    	$output .= "<div class='card'>";
	    	$output .= "<div class='block_title'><a href='{$data['link']}'>{$data['title']}</a></div>";
	    	$output .= "<div class='block_body'>{$data['excerpt']}</div>";
	    	$output .= "<div class='block_footer'>{$data['post_type']}</div>";
	    	$output .= "</div>";
	    	$output .= "</article>";
	    }
	    // wp_reset_postdata();  
	} else {
	    $output .= "<div>Content not found.</div>";	}

	wp_reset_postdata();
	
	return $output;	
}

//[nflrc_contact_grid]
/* Displays all contact post types filtered by shortcode param.

Attributes:
role_type - STAFF (default), ADVBOARD, COLLAB
cls_str - horizontal or vertical (vertical) layout for each card

Example: [nflrc_contact_grid role_type="STAFF"]
*/
add_shortcode('nflrc_contact_grid', 'nflrc_contact_grid_func');
function nflrc_contact_grid_func($atts, $content = null) {
	$a = shortcode_atts( array(
		'role_type' => 'STAFF',
		'cls_str' => 'vertical',
	), $atts );
	$role_type = sanitize_text_field($a['role_type']);
	$args = array(
		// 'numberposts' 		=> 1000,
		// 'order'   			=> 'DESC',
		'meta_key'     	=>'nflrc_staff',
		'meta_value'	=> 't',
	    'post_type'      	=> 'contact',
	    'posts_per_page' 	=> -1,

	);
	$posts = new WP_Query($args);
	$output = '';
	
	if ( $posts->have_posts() ) {
		$output .= "<div class='grid_wrap'>";
	    global $post;
	    while ( $posts->have_posts() ) {
	    	$posts->the_post();
	    	$data = read_nflrc_fields($post);
	    	
	    	$output .= "<article class='grid_block {$a['cls_str']}'>";
	    	$output .= "<div><a href='{$data['link']}'>{$data['icon']}</a></div>";
	    	$output .= "<div class='card'>";
	    	$output .= "<div class='block_title'><a href='{$data['link']}'>{$data['title']}</a></div>";
	    	$output .= "<div class='block_body'>{$data['nflrc_staff']} | {$data['nflrc_role_type']} | {$data['excerpt']} </div>";
	    	$output .= "<div class='block_footer'>{$data['nflrc_role']}</div>";
	    	$output .= "</div>";
	    	$output .= "</article>";
	    	// var_dump($data);
	    }
	    $output .= "</div>";
	    // wp_reset_postdata();  
	} else {
	    $output .= "<div>Content not found.</div>";	}

	wp_reset_postdata();
	
	return $output;	
}

add_shortcode('nflrc_meta_display', 'nflrc_meta_display_func');
function nflrc_meta_display_func() {
	global $post;
	$d = read_nflrc_fields($post);
	// $here = get_the_permalink();
	// $html = "<div class='fb-like' data-href='http://nflrc.hawaii.edu' data-width=' data-layout='standard' data-action='like' data-size='small' data-share='true'></div>";	
	$html .= "<div class='item-meta-list'>";	

	if($d['post_type'] === 'project') {
		$grant_cycle = get_the_term_list($post->ID, 'grant_period', ' ', '|');
		$languages = get_the_term_list($post->ID, 'language', ' ', '|');
		$html .= "<ul class='stacked'>";
		$html .= "<li>Project Lead(s): {$d['director']}</li>";
		$html .= "<li>Period: {$grant_cycle}</li>";
		$html .= "<li>{$languages}</li>";
		$html .= "</ul>";

	} else if ($d['post_type'] === 'publication') {
		
		$html .= "<p>{$d['author']}</p>";
		// $resource_type = get_the_terms($post->ID, ['resource_type']);
		// if ($resource_type) {
		// 	$html .= "<p>";
		// 	foreach ($resource_type as $term) $html .= " {$term} ";
		// 	$html .= "</p>";
		// }
		
		$html .= "<ul class='meta-bar'>";
		if ($d['oer']) $html .= "<li><img src='/wp-content/uploads/2020/09/oer-logo.png' alt='OER logo'></li> ";
		
		if ($d['oclc_url']) $html .= "<li><button class='basic-btn btn-default'><a href='{$d['oclc_url']}' target='_blank'><img src='/wp-content/uploads/2020/11/oclc-logo.png' alt='OCLC link'></a></button></li> ";	
				
		if ($d['url']) $html .= "<li><button class='basic-btn btn-default'><a href='{$d['url']}' target='_blank'>Access <i class='fas fa-external-link-alt'></i></a></button></li>";		
		
		if ($d['ext_url']) $html .= "<li><button class='basic-btn btn-default'><a href='{$d['ext_url']}' target='_blank'>More Info <i class='fas fa-external-link-alt'></i></a></button></li> ";
		
		if ($d['order_url']) $html .= "<li><button class='basic-btn btn-default'><a href='{$d['order_url']}' target='_blank'>Buy</a></button></li> ";
		$html .= "</ul>";

		if ($d['apa_citation']) $html .= "<p class='citation'>{$d['apa_citation']}</p>";

	} else if ($d['post_type'] === 'prodev') {
		$html .= "<ul class='stacked'>";
		$html .= "<li>{$d['event_date']}</li>";
		$html .= "<li>Project Lead(s): {$d['director']}</li>";
		if ($d['facilitator']) $html .= "<li>Facilitator(s): {$d['facilitator']}</li>";
		if ($d['url']) $html .= "<li><a href='{$d['url']}' target='_blank'>More info</a></li>";
		
		$html .= "</ul>";

	} else if ($d['post_type'] === 'contact') {
		$html .= "";

	} else if ($d['post_type'] === 'story') {
		$html .= "";
	}

	$html .= "</div>";
	return $html;
}

add_shortcode('nflrc_item_tags', 'nflrc_item_tags_func');
function nflrc_item_tags_func() {
	global $post;
	
    $html .=    "<div class='tags'>";
    $html .=    	get_the_term_list($post->ID, ['focus_area', 'language', 'professional_learning', 'resource_type', 'grant_period'], '<ul><li>', '</li><li>', '</li></ul>');
    $html .=    "</div>";
	return $html;
}

add_shortcode('nflrc_item_categories', 'nflrc_item_categories_func');
function nflrc_item_categories_func() {
	global $post;
	
    $html .=    "<div class='cats'>";
    $html .=    	get_the_term_list($post->ID, 'category', ' ', ' ');
    $html .=    "</div>";
	return $html;
}

add_shortcode('nflrc_mod_all_post_dates', 'nflrc_mod_post_dates_func');
function nflrc_mod_post_dates_func() {
		global $wpdb;
		$args = array(
			'post_type' => array('contact'),
			// 'meta_key' 	=> 'grant_cycle',
			// 'orderby'	=> 'grant_cycle',
	  //   	'order'   	=> 'DESC',
			'posts_per_page' => -1,
		);
		$the_query = new WP_Query( $args );
		$debugstr = "";
		$debugstr .= "<h2>" . $the_query->post_count . " posts</h2>";
		if ( $the_query->have_posts() ) {
			global $post;
		    while ( $the_query->have_posts() ) {
		        $the_query->the_post();
		        $d = read_nflrc_fields($post);
		        // $title = $post->post_title;
		        // $p_id = $post->ID;
		        // $grant = $post->grant_cycle;
		        // $wpdb->query("UPDATE $wpdb->posts SET post_date = '2000-01-01 00:00:00.000000', post_date_gmt = '2000-01-01 00:00:00.000000'  WHERE ID = {$post->ID}");
		        
		        	        
		        $debugstr .= "<div>{$d['cycle']} | {$d['title']} | </div>";
		    }
		    wp_reset_postdata();    
		} 
		
		return $debugstr;
}

add_shortcode('import_csv_post_dates', 'import_csv_post_dates_func');
function import_csv_post_dates_func($atts, $content = null) {
  global $wpdb;
  if (isset($_POST['submit'])) {
    $csv_file = $_FILES['csv_file'];
    $csv_to_array = array_map('str_getcsv', file($csv_file['tmp_name']));
	$output = "";
	$count = 0;
    foreach ($csv_to_array as $key => $value) {
    	// var_dump($value);
    	// [0]6, [1]"project", [2]"1996-08-01 00:00:00.000000"
		$args = array(
			'post_type'      	=> $value[1],
			'meta_key'       	=> 'postgres_pk',
			'meta_value'		=> $value[0],
		    'numberposts' 		=> 1,
		);
		$posts = new WP_Query($args);

		if ( $posts->have_posts() ) {
			$count += 1;
			global $post;
		    $posts->the_post();
		    // $wpdb->query("UPDATE $wpdb->posts SET post_date = '{$value[2]}', post_date_gmt = '{$value[2]}'  WHERE ID = {$post->ID}");
		    $output .= "<div>{$post->ID} {$value[0]} {$value[1]} {$value[2]} </div>";
		}
		wp_reset_postdata();
	}
	$output .= $count;
	return $output;
  } else {
  	echo '<h2>Import timestamp info from legacy site</h2>';
    echo '<form action="" method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="csv_file">';
    echo '<input type="submit" name="submit" value="submit">';
    echo '</form>';
  }
}

add_shortcode('import_csv_apa_cites', 'import_csv_apa_cites_func');
function import_csv_apa_cites_func($atts, $content = null) {
  
  if (isset($_POST['submit'])) {
    $csv_file = $_FILES['csv_file'];
    $csv_to_array = array_map('str_getcsv', file($csv_file['tmp_name']));
	$output = "";
	$count = 0;
    foreach ($csv_to_array as $key => $value) {
    	// var_dump($value);
    	// [0]6, [1]"project", [2]"1996-08-01 00:00:00.000000"
		$args = array(
			'post_type'      	=> 'publication',
			'meta_key'       	=> 'item_number',
			'meta_value'		=> $value[0],
		    'numberposts' 		=> 1,
		);
		$posts = new WP_Query($args);

		if ( $posts->have_posts() ) {
			$count += 1;
			global $post;
		    $posts->the_post();
		    // update_post_meta( $post->ID, 'apa_citation', $value[1] );
		    $output .= "<div>{$post->ID} {$value[0]} {$value[1]}</div>";
		}
		wp_reset_postdata();
	}
	$output .= $count;
	return $output;
  } else {
  	echo '<h2>Import apa citations from csv file to write to apa_citation field in publications.</h2>';
    echo '<form action="" method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="csv_file">';
    echo '<input type="submit" name="submit" value="submit">';
    echo '</form>';
  }
}
add_shortcode('nflrc_dump_post_info', 'nflrc_dump_post_info_func');
function nflrc_dump_post_info_func() {


		

		$args = array(
			// 'post_type' => array('project', 'prodev', 'publication', 'contact', 'story'),
			'post_type' => array('story'),
			'orderby'	=> 'ID',
	    	// 'order'   	=> 'DESC',
			'posts_per_page' => -1,
		);
		$the_query = new WP_Query( $args );
		$debugstr = "";
		$debugstr .= "<h2>" . $the_query->post_count . " posts</h2>";
		if ( $the_query->have_posts() ) {
			global $post;
		    while ( $the_query->have_posts() ) {
		        $the_query->the_post();
		        // $d = read_nflrc_fields($post);
		        // $title = $post->post_title;
		        // $p_id = $post->ID;
		        // $grant = $post->grant_cycle;
				// $my_post = array(
		  //     		'ID'          => $post->ID,
		  //     		'post_name'   => $post->postgres_pk,
		  //     	);
		      	// wp_update_post( $my_post );
		        	        
		        $debugstr .= "<div>{$post->post_type}, {$post->ID}, {$post->guid}, {$post->post_name}, {$post->postgres_pk}, {$post->item_number}</div>";
		    }
		    wp_reset_postdata();
		} else {
			$output = array(); 
		}
		// var_dump($output);
		return $debugstr;
}

add_shortcode('nflrc_debug', 'nflrc_debug_func');
function nflrc_debug_func() {
		// $t = '2018-2022';
		$args = array(
		    'post_type' => array('project', 'prodev', 'publication', 'contact', 'story'),
		    'posts_per_page' 	=> -1,
		);
		$the_query = new WP_Query( $args );
		$output = array();
		$debugstr = "Debugging output ...";
		$debugstr .= "<h2>" . $the_query->post_count . "</h2>";
		if ( $the_query->have_posts() ) {
			global $post;
			$count = 0;
		    while ( $the_query->have_posts() ) {
		        $the_query->the_post();
		        
		        $d = read_nflrc_fields($post);
		        $post_type = $post->post_type;
		        $category = $post->category;
		        $tags = get_the_terms($post->ID, ['focus_area', 'language', 'professional_learning', 'resource_type', 'grant_period']);
		        // $oertag = has_term('OER', 'resource_type');
		        if ($tags) {	
		        	$count = $count + 1;        
		        	// update_post_meta( $post->ID, 'is_oer', true );
		        	$debugstr .= "<div>";
		        	$liststr = "";
		        	foreach ( $tags as $term ) {
		        		$liststr .= "{$term->name}, ";
		        	}
					// $my_post = array(
			  //     		'ID'       => $post->ID,
			  //     		'cfield'   => $liststr,
			  //     	);
			      	// update_post_meta( $post->ID, 'tag_history', $liststr );	

		        	$debugstr .= "{$post->ID} | {$liststr} </div>";
		        } else {

		        	$debugstr .= "no tags";
		        	// update_post_meta( $post->ID, 'is_oer', false );
		        }
		        

		        /*$debugstr .= "<article class='grid_block'>";
				$debugstr .= "<div>{}</div>";
				$debugstr .= "<div class='card'>";
				$debugstr .= "<div class='block_title'>{}</div>";
				$debugstr .= "<div class='block_body'>{}</div>";
				$debugstr .= "<div class='block_footer'>{}</div>";
				$debugstr .= "</div>";
				$debugstr .= "</article>";*/
		    }

		    wp_reset_postdata();    
		    $debugstr .= "<h2>" . $count . "</h2>";
		} else {
			$output = array(); 
		}
		// var_dump($output);
		return $debugstr;
}
/*Set taxonomy term for a post
wp_set_post_terms

Get posts with tag:
$query = new WP_Query( array( 'tag' => 'cooking' ) );


pedagogy
40,17,18,41,15,16,35,127
assessment
11,12
Professional Learning
129,131,133,32,33,130,132
publications
used publication table
resource_types for language teaching materials
136,137,140,144,39,135
research
19

.dp-dfg-dropdown-tag
background-color: #3f5ca9;
color: white;

*/


