<?php
add_action('rest_api_init', 'register_custom_routes');
function register_custom_routes() {
    register_rest_route('wp/v2', 'custom-categories', array(
        'methods' => 'GET',
        'callback' => 'get_custom_categories',
       
    ));

    register_rest_route('wp/v2', 'getFeaturedPost', array(
        'methods' => 'GET',
        'callback' => 'get_custom_posts',
		'args'     => array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            ),
			),
        ));
		
		register_rest_route('wp/v2', 'getsidefeaturedpost', array(
        'methods' => 'GET',
        'callback' => 'get_custom_posts_side',
		'args'     => array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            ),
			),
        ));
	register_rest_route('wp/v2', 'nocatgetFeaturedPost', array(
        'methods' => 'GET',
        'callback' => 'nocat_get_custom_posts',
		'args'     => array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            ),
			),
        ));
		
		register_rest_route('wp/v2', 'nocatgetsidefeaturedpost', array(
        'methods' => 'GET',
        'callback' => 'nocat_get_custom_posts_side',
		'args'     => array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            ),
			),
        ));	
}

// Callback function to retrieve custom categories
function get_custom_categories() {
    $categories = get_terms(array(
        'taxonomy' => 'category', // Adjust taxonomy if needed
        'hide_empty' => true,
    ));

    $formatted_categories = array();

    foreach ($categories as $category) {
        $formatted_categories[] = array(
            'name' => $category->name,
            'id' => $category->term_id,
        );
    }

    return rest_ensure_response($formatted_categories);
}

// Callback function to retrieve custom posts based on category
function get_custom_posts($data) {
    $category_id = $data->get_param('catId');

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 1,
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'post_status' => 'publish',
		'offset'            => 0,
         'meta_query' => array(
            array(
                'key' => 'featured_post',
                'value' => 'on',
            ),
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'id',
                'terms' => $category_id,
            ),
        ), 
    );

    //$query = new WP_Query($args);
    $posts = get_posts($args);

    $formatted_posts = array();

    foreach ($posts as $post) {
        $featured_image_id = get_post_thumbnail_id($post->ID);
        $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');

        $formatted_posts[] = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'excerpt' => $post->post_excerpt,
            'featured_image' => $featured_image_url,
        );
    }

    return rest_ensure_response($formatted_posts);
}

function get_custom_posts_side($data) {
    $category_id = $data->get_param('catId');

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 2,
		'post_status' => 'publish',
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'offset'            => 1,
         'meta_query' => array(
            array(
                'key' => 'featured_post',
                'value' => 'on',
            ),
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'id',
                'terms' => $category_id,
            ),
        ), 
    );

    //$query = new WP_Query($args);
    $posts = get_posts($args);

    $formatted_posts = array();

    foreach ($posts as $post) {
        $featured_image_id = get_post_thumbnail_id($post->ID);
        $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');

        $formatted_posts[] = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'excerpt' => $post->post_excerpt,
            'featured_image' => $featured_image_url,
        );
    }

    return rest_ensure_response($formatted_posts);
}

function nocat_get_custom_posts($data) {
    //$category_id = $data->get_param('catId');

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 1,
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'post_status' => 'publish',
		'offset'            => 0,
         'meta_query' => array(
            array(
                'key' => 'featured_post',
                'value' => 'on',
            ),
        ),
     );

    //$query = new WP_Query($args);
    $posts = get_posts($args);

    $formatted_posts = array();

    foreach ($posts as $post) {
        $featured_image_id = get_post_thumbnail_id($post->ID);
        $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');

        $formatted_posts[] = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'excerpt' => $post->post_excerpt,
            'featured_image' => $featured_image_url,
        );
    }

    return rest_ensure_response($formatted_posts);
}

function nocat_get_custom_posts_side($data) {
   

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 2,
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'post_status' => 'publish',
		'offset'            => 1,
         'meta_query' => array(
            array(
                'key' => 'featured_post',
                'value' => 'on',
            ),
        ),
        
    );

    //$query = new WP_Query($args);
    $posts = get_posts($args);

    $formatted_posts = array();

    foreach ($posts as $post) {
        $featured_image_id = get_post_thumbnail_id($post->ID);
        $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');

        $formatted_posts[] = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'excerpt' => $post->post_excerpt,
            'featured_image' => $featured_image_url,
        );
    }

    return rest_ensure_response($formatted_posts);
}

?>