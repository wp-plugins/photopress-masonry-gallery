<?php

/*
Plugin Name: PhotoPress - Masonry Gallery
Plugin URI: Permalink: http://www.peteradamsphoto.com/?page_id=3148
Description: Adds a Masonry gallery presentation to the core gallery shortcode.
Author: Peter Adams
Version: 1.0
Author URI: http://www.peteradamsphoto.com 
*/

class photopress_masonry_gallery {
	
	static $admin_notices = array();
	static $enabled;
	
	static function init() {
	
		add_action('admin_notices', array( 'photopress_masonry_gallery', 'admin_notices' ) );
		
		// test for dependant photopress plugins
		
		
		if ( ! self::checkForDependants() ) {
			self::addAdminNotice(
				sprintf(
					__('PhotoPress Masonry Gallery relies on the <a href="%s">PhotoPress Gallery</a> plugin, please install this plugin.', 'photopress_masonry_gallery'), 'http://wordpress.org/plugins/photopress-gallery/'
				),
			'error');
		}
	}
	
	static function checkForDependants() {
		
		// test for dependant WordPress Simple Shopping Cart Plugin
		self::$enabled = function_exists('photopress_gallery_shortcode');
		return self::$enabled;
	}
	
	/**
	 * Append a message of a certain type to the admin notices.
	 *
	 * @param string $msg 
	 * @param string $type 
	 * @return void
	 */
	static function addAdminNotice( $msg, $type = 'updated' ) {
	
		self::$admin_notices[] = array(
			'type' => $type == 'error' ? $type : 'updated', // If it's not an error, set it to updated
			'msg' => $msg
		);
	}
	
	/**
	 * Displays admin notices 
	 *
	 * @return void
	 */
	static function admin_notices() {
		
		if ( is_array( self::$admin_notices ) ) {
		
			foreach ( self::$admin_notices as $notice ) {
				extract( $notice );
				?>
				<div class="<?php echo esc_attr($type); ?>">
					<p><?php echo $msg; ?></p>
				</div><!-- /<?php echo esc_html($type); ?> -->
				<?php
			}
		}
	}
	
	static function registerDependantActions() {
		
		if ( self::checkForDependants() ) {
		
			// filters shortcode attributes for various features
			add_filter( 'shortcode_atts_gallery', 'photopress_masonry_gallery_shortcode_attrs', 10, 3 );
			// enqueue various javascript libs
			add_action( 'wp_enqueue_scripts', 'photopress_masonry_gallery_scripts' );		
		}
	}
}


function photopress_masonry_gallery_post_output( $output, $selector, $attr ) {
	
	if ( isset( $attr['type'] ) && $attr['type'] === 'masonry' ) {
	
		$output .= "
		
		<script>
		
		// make sure the dom is ready.
		jQuery( function( $ ){
	
			( function () {
				// create new masonry gallery
				photopress.galleries['$selector'] = new photopress.gallery.masonry('#$selector');
				// render gallery
				photopress.galleries['$selector'].render();	
		
			}()); 
		});
		
		</script>
		<!-- End PhotoPress Masonry Gallery -->
		";
	}
	
	// needed just in case other galleries on the same page need the default styles.
	add_filter( 'use_default_gallery_style', '__return_true' );
	
	return $output;
}

/**
 * Changes the gallery shortcode attrs to support various gallery types
 *
 */
function photopress_masonry_gallery_shortcode_attrs( $out, $pairs, $attr ) {
	
	// Attributes needed for sideways gallery markup
	if ( isset( $out['type'] ) && $out['type'] === 'masonry' ) {
		
		$out['container_class'] = 'photopress-gallery-masonry';
		// adds post markup
		add_filter( 'use_default_gallery_style', '__return_false' );
		add_filter( 'post_gallery_post_output', 'photopress_masonry_gallery_post_output', 99, 3 );
	} else {
		// needed in case a there are multiple galleries on the same page.
		//add_filter( 'use_default_gallery_style', '__return_true' );
	}
	
	// always need to return the full set of attrs
	return $out;
}



/**
 * Hook callback for addingthe Sly javascrpt lib
 *
 */
function photopress_masonry_gallery_scripts() {
	
	// needed for masonry galleries
	wp_enqueue_script(
		'imagesloaded',
		plugins_url( 'js/jquery.imagesloaded.js' , __FILE__ ),
		array( 'jquery' )
	);
	
	// needed for masonry galleries
	wp_enqueue_script(
		'masonry',
		plugins_url( 'js/jquery.masonry.min.js' , __FILE__ ),
		array( 'jquery', 'imagesloaded' )
	);
	
	// main photopress js lib
	wp_enqueue_script(
		'photopress-masonry-gallery',
		plugins_url( 'js/photopress-masonry-gallery.js' , __FILE__ ),
		array( 'jquery', 'imagesloaded', 'masonry' )
	);
	
	wp_register_style( 
		'photopress-masonry-gallery', 
		plugins_url('css/photopress-masonry-gallery.css',
		 __FILE__) 
	);
    
    wp_enqueue_style( 'photopress-masonry-gallery' );
    
}

add_action( 'init', array('photopress_masonry_gallery', 'init' ), 98 );
add_action( 'plugins_loaded', array('photopress_masonry_gallery', 'registerDependantActions' ) );

?>