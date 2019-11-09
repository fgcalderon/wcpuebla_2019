<?php

	//Add styles and JS
	function fc_enqueue_styles() {
	    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
		wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );

		wp_enqueue_script('app', get_stylesheet_directory_uri() .'/js/app.js', array('jquery'), '09', true);

	}

	add_action( 'wp_enqueue_scripts', 'fc_enqueue_styles' );


	//Customize Login screen
	function fc_login_logo() {
	    wp_register_style( 'admin-styles',  get_stylesheet_directory_uri().'/css/admin.css', array(), null);
	    wp_enqueue_style( 'admin-styles');
	}

	add_action( 'login_enqueue_scripts', 'fc_login_logo' );


	//Redirect to specific page after login
	function fc_redirect_to() {
	  return '/mis-documentos';
	}

	add_filter('login_redirect', 'fc_redirect_to');


	//Redirect to specific page after logout
	function fc_redirect_after_logout(){
		wp_redirect( home_url() );
		exit();
	}

	add_action('wp_logout','fc_redirect_after_logout');


	//Hide admin bar for non admin users
	if ( ! current_user_can( 'manage_options' ) ) {
	 add_filter('show_admin_bar', '__return_false');
	}



	// Function to change email address

	function wpb_sender_email( $original_email_address ) {
	    return 'example@mywebsite.com';
	}

	// Function to change sender name
	function wpb_sender_name( $original_email_from ) {
	    return 'Francisco Calderón';
	}

	// Hooking up our functions to WordPress filters
	add_filter( 'wp_mail_from', 'wpb_sender_email' );
	add_filter( 'wp_mail_from_name', 'wpb_sender_name' );



	// Add Opengraph metadata
	function fc_add_opengraph_metas() {
	    global $post;
	    setup_postdata( $post );
	    $output =  "\n".'<!-- OPENGRAPH -->'. "\n";

	    if ( is_singular() ) {
	        $output .= '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '" />' . "\n";
	        $output .= '<meta property="og:type" content="article" />' . "\n";
	        $output .= '<meta property="og:url" content="' . get_permalink() . '" />' . "\n";
	        $output .= '<meta property="og:description" content="' . esc_attr( get_the_excerpt() ) . '" />' . "\n";
	        if ( has_post_thumbnail() ) {
	            $imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
	            $output .= '<meta property="og:image" content="' . $imgsrc[0] . '" />' . "\n\n";
	        }else{
	            $output .= '<meta property="og:image" content="' . IMAGES . '/logo.png" />' . "\n\n";
	        }
	    }else{
	        $output .= '<meta property="og:title" content="' . SITENAME . '" />' . "\n";
	        $output .= '<meta property="og:type" content="website" />' . "\n";
	        $output .= '<meta property="og:url" content="' . HOMELINK . '" />' . "\n";
	        $output .= '<meta property="og:description" content="' . get_bloginfo('description') . '" />' . "\n";
	        $output .= '<meta property="og:image" content="' . IMAGES . '/logo.png" />' . "\n\n";
	    }
	    echo $output;
	}
	add_action( 'wp_head', 'fc_add_opengraph_metas' );




	//Fix upload image error
	function wpb_image_editor_default_to_gd( $editors ) {
	    $gd_editor = 'WP_Image_Editor_GD';
	    $editors = array_diff( $editors, array( $gd_editor ) );
	    array_unshift( $editors, $gd_editor );
	    return $editors;
	}
	add_filter( 'wp_image_editors', 'wpb_image_editor_default_to_gd' );


	//Shortcode example
	function latest_posts_pc_func ($atts) {

		$atts = shortcode_atts( array(
	          //'category'   => 'blog',
	          'cant'    => 3 // How many posts to show
	     ), $atts );



	    $args = array(
	        //'category_name'	 => $atts['category'],
	        'posts_per_page' => $atts['cant']
	    );

	    $query = new WP_Query( $args );
	    if ( $query->have_posts() ) {

	        $html = "<ul class='et_pb_widget latest_posts_pc'>";
	                        while ( $query->have_posts() ) :
	                            $query->the_post();

	                            $html .=  "<li> ";
	                             $html.="<a href='".get_the_permalink()."'>".get_the_post_thumbnail($query->post->ID,'thumbnail',array('class'=>'post-img'))."</a>";
	                                            $html .= "<a href='".get_the_permalink()."''>".get_the_title()."</a>";
	                            $html .= "</li>";
	                        endwhile;
	        $html .=    "</ul>";
	    }else{
	        $html .= "<div>No hay información para mostrar</div>";
	    }
	    wp_reset_postdata();
	    return $html;
	}
	add_shortcode( 'latest_posts', 'latest_posts_pc_func' );



	//Remove Meta Tags
	remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
	remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
	remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
	remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
	remove_action( 'wp_head', 'index_rel_link' ); // index link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
	remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
	remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version


?>