<?php
// Register the block
function enqueue_latest_news_block_assets() {
    wp_enqueue_style(
        'latest-news-block-style',
        plugins_url('style.css', __FILE__),
        array('wp-edit-blocks')
    );

    wp_register_script(
        'latest-news-block',
        plugins_url('latest-news-block.js', __FILE__),
        array('wp-blocks', 'wp-editor', 'wp-components', 'wp-api') // Original dependencies
    );

    if(is_user_logged_in()){
        // Enqueue the script
        wp_enqueue_script('latest-news-block');
    }
}

add_action('enqueue_block_assets', 'enqueue_latest_news_block_assets');

// Fetch posts based on the selected category
function fetch_latest_news_posts($attributes) {
    $selectedCategory = $attributes['selectedCategory'];
	$output = '<section class="news-landing-header"><div class="news-hero-wrapper">';
    if ($selectedCategory) {
        // Get the category ID from the slug
        //$category = get_category_by_slug($selectedCategory);

        if ($selectedCategory) {
            //$categoryId = $category->term_id;
            

            $args = array(
				'post_type'      => 'post',
				'orderby'        => 'post_date',
				'order'          => 'DESC',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'offset'         => 0,
				'tax_query'      => array(
					array(
						'taxonomy' => 'category',
						'field'    => 'id',
						'terms'    => $selectedCategory,
					),
				),
				'meta_query'     => array(
					array(
						'key'     => 'featured_post',  // Replace with your actual meta key
						'value'   => 'on',  // Replace with your actual meta value
						'compare' => '=',   // Use '=' for exact match
					),
				),
			);

            $community_posts = new WP_Query($args);

            if ($community_posts->have_posts()) {
                while ($community_posts->have_posts()) {
                    $postcom = $community_posts->the_post();
                    $postid = get_the_ID();
                    $post_thumbnail_id = get_post_thumbnail_id($postid);
                    $thumbnail_url = wp_get_attachment_url($post_thumbnail_id);
                    $title = get_the_title($postid);
                    $permalink = get_the_permalink($postid);
                    $excerpt = get_the_excerpt($postid);
					$content = substr($excerpt, 0, 100);
                    $output .= '<a class="news-hero-featured" href="' . $permalink . '" alt=".." style="background-image: url(' . $thumbnail_url . ');">
                        <span class="featured-hero-link">
                            <span class="featured-hero-link-inner">
                                <h2>' . $title . '</h2>
                                <p>' . $content .'[..]</p>
                            </span>
                        </span>
                    </a>';
                }

                // Restore original post data
                wp_reset_postdata();
            } else {
                // No posts found
            }

            $output .= '<div class="news-hero-supporting">';
           $args = array(
					'post_type'      => 'post',
					'orderby'        => 'post_date',
					'order'          => 'DESC',
					'post_status'    => 'publish',
					'posts_per_page' => 2,
					'offset'         => 1,
					'tax_query'      => array(
						array(
							'taxonomy' => 'category',
							'field'    => 'id',
							'terms'    => $selectedCategory,
						),
					),
					'meta_query'     => array(
						array(
							'key'     => 'featured_post',  // Replace with your actual meta key
							'value'   => 'on',  // Replace with your actual meta value
							'compare' => '=',   // Use '=' for exact match
						),
					),
				);

            $community_posts = new WP_Query($args);

            if ($community_posts->have_posts()) {
                while ($community_posts->have_posts()) {
                    $postcom = $community_posts->the_post();
                    $postid = get_the_ID();
                    $post_thumbnail_id = get_post_thumbnail_id($postid);
                    $thumbnail_url = wp_get_attachment_image_url($post_thumbnail_id);
                    $title = get_the_title($postid);
                    $permalink = get_the_permalink($postid);
                    $excerpt = get_the_excerpt($postid);
					$content = substr($excerpt, 0, 100);
                    $output .= '<a class="hero-link" href="' . $permalink . '" alt=".." style="background-image: url(' . $thumbnail_url . ');">
                        <span class="hero-link-inner">
                            <h2>' . $title . '</h2>
                           <p>' . $content .'[..]</p>
                        </span>
                    </a>';
                }

                // Restore original post data
                wp_reset_postdata();
            } else {
                // No posts found
            }

            $output .= '</div>
                    </div>
                </section>';
        } else {
            $output = '<p>Invalid category</p>';
        }
    } else {
        //$output = '<p>No category selected</p>';
		$output = '<section class="news-landing-header"><div class="news-hero-wrapper">';

            $args = array(
				'post_type'      => 'post',
				'orderby'        => 'post_date',
				'order'          => 'DESC',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'offset'         => 0,
				
				'meta_query'     => array(
					array(
						'key'     => 'featured_post',  
						'value'   => 'on',  
						'compare' => '=',   
					),
				),
			);

            $community_posts = new WP_Query($args);

            if ($community_posts->have_posts()) {
                while ($community_posts->have_posts()) {
                    $postcom = $community_posts->the_post();
                    $postid = get_the_ID();
                    $post_thumbnail_id = get_post_thumbnail_id($postid);
                    $thumbnail_url = wp_get_attachment_url($post_thumbnail_id);
                    $title = get_the_title($postid);
                    $permalink = get_the_permalink($postid);
                    $excerpt = get_the_excerpt($postid);
					$content = substr($excerpt, 0, 100);

                    $output .= '<a class="news-hero-featured" href="' . $permalink . '" alt=".." style="background-image: url(' . $thumbnail_url . ');">
                        <span class="featured-hero-link">
                            <span class="featured-hero-link-inner">
                                <h2>' . $title . '</h2>
                                <p>' . $content .'[..]</p>
                            </span>
                        </span>
                    </a>';
                }

                // Restore original post data
                wp_reset_postdata();
            } else {
                // No posts found
            }
			$output .= '<div class="news-hero-supporting">';
           $args = array(
					'post_type'      => 'post',
					'orderby'        => 'post_date',
					'order'          => 'DESC',
					'post_status'    => 'publish',
					'posts_per_page' => 2,
					'offset'         => 1,
					
					'meta_query'     => array(
						array(
							'key'     => 'featured_post',  // Replace with your actual meta key
							'value'   => 'on',  // Replace with your actual meta value
							'compare' => '=',   // Use '=' for exact match
						),
					),
				);

            $community_posts = new WP_Query($args);

            if ($community_posts->have_posts()) {
                while ($community_posts->have_posts()) {
                    $postcom = $community_posts->the_post();
                    $postid = get_the_ID();
                    $post_thumbnail_id = get_post_thumbnail_id($postid);
                    $thumbnail_url = wp_get_attachment_image_url($post_thumbnail_id);
                    $title = get_the_title($postid);
                    $permalink = get_the_permalink($postid);
                    $excerpt = get_the_excerpt($postid);
					$content = substr($excerpt, 0, 100);
                    $output .= '<a class="hero-link" href="' . $permalink . '" alt=".." style="background-image: url(' . $thumbnail_url . ');">
                        <span class="hero-link-inner">
                            <h2>' . $title . '</h2>
                            <p>' . $content .'[..]</p>
                        </span>
                    </a>';
                }

                // Restore original post data
                wp_reset_postdata();
            } else {
                // No posts found
            }

            $output .= '</div>
                    </div>
                </section>';
    }

    return $output;
}

// Register server-side rendering callback
register_block_type('latest-news-block/latest-news', array(
    'render_callback' => 'fetch_latest_news_posts',
));
