<?php

/**
 * [MangUI]	ManguiThemeSetup Class
 *
 * This class sets up the WordPress theme with various settings
 *
 * @package     mangui
 * @category    setup
 * @since       1.0.0.alpha.4
 * @version     1.0.0.alpha.4
 * @author      Andi North <andi@mangopear.co.uk>
 * @link        https://mangopear.co.uk/mangui/
 * @license     GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 */


/**
 * CHANGELOG
 *
 * @version 1.0.0.alpha.4
 *          Init class
 */


/**
 * CONTENTS
 *
 * [1]  Forbid direct loading of this file
 * [2]	Define class
 * [3]	Initialise
 */


/**
 * [1]	Forbid direct loading of this file
 */

if (! defined('ABSPATH')) { exit; }





/**
 * [2]	Define class
 *
 * 		[a]	Initialise filters, actions, variables and includes
 * 		[b]	After theme activation
 * 		[c]	Remove certain links from the admin menu
 * 		[d]	Prevent WordPress from adding links to inserted media by default
 * 		[e]	Enqueue our styles and scripts
 * 		[f]	Function to remove jump links from post content
 * 		[g]	Add framework specific classes to inserted images
 * 		[h]	Function to remove query string
 */

if (! class_exists('ManguiThemeSetup')) :
	class ManguiThemeSetup {


		/**
		 * [a]	Initialise filters, actions, variables and includes
		 *
		 * 		@since  1.2.0
		 *
		 * 		[i]		Remove default template actions to reduce meta in <head>
		 * 		[ii]	Hook our class functions into the after theme setup action
		 * 		[iii]	Remove certain links from the admin menu
		 * 		[iv]	When inserting media into a post, stop the default settings for adding a link
		 * 		[v]		Enqueue the MangUI default CSS and JS files
		 * 		[vi]	Allow shortcodes to be executed within widgets.
		 * 		[vii]	Function to remove jump links from post content
		 * 		[viii]	Add framework specific classes to inserted images
		 * 		[ix]	Function to remove query string
		 */
		
		public function __construct() {
			remove_action('wp_head', 'rsd_link');																// [i]
			remove_action('wp_head', 'wlwmanifest_link');														// [i]
			remove_action('wp_head', 'wp_generator');															// [i]
			remove_action('wp_head', 'wp_shortlink_wp_head');													// [i]
			

			add_action('after_setup_theme', 	array($this, 'mangui_startr_setup'));							// [ii]
			add_action('admin_menu', 			array($this, 'mangui_startr_hide_pages'));						// [iii]
			add_action('admin_init',	 		array($this, 'mangui_startr_no_image_links'), 10);				// [iv]
			add_action('wp_enqueue_scripts', 	array($this, 'mangui_startr_enqueue'));							// [v]


			add_filter('widget_text', 			'do_shortcode');												// [vi]
			add_filter('after_theme_setup', 	array($this, 'mangui_startr_remove_jump_links'));				// [vii]
			add_filter('get_image_tag_class', 	array($this, 'mangui_startr_filter_image_html'), 	 0, 4);		// [viii]
			add_filter('script_loader_src', 	array($this, 'mangui_startr_remove_query_strings'), 15, 1);		// [ix]
			add_filter('style_loader_src',  	array($this, 'mangui_startr_remove_query_strings'), 15, 1);		// [ix]
		}





		/**
		 * [b]	After theme activation
		 *
		 * 		@since  1.2.0
		 *
		 * 		[i] 	Enable a languages directory to allow theme to be translatable
		 * 		[ii]	Add support for featured images
		 * 		[iii]	Enable automatic links for feeds
		 * 		[iv]	Enable support for HTML5 versions of WordPress tools and widgets
		 * 		[v]		Add support for post formats
		 * 		[vi]	Add an editor style - you'll need to add a CSS document to theme root for styles to apply
		 * 		[vii]	Prevent files from being editable from CMS - this is a good security and user-proofing method
		 */
		
		public function mangui_startr_setup() {
			load_theme_textdomain('mangui', get_template_directory() . '/languages');														// [i]


			add_theme_support('post-thumbnails');																							// [ii]
			add_theme_support('automatic-feed-links');																						// [iii]
			add_theme_support('html5', array('comment-list', 'search-form', 'comment-form', 'gallery', 'caption'));							// [iv]
			add_theme_support('post_formats', array('aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'));		// [v]


			add_editor_style();																												// [vi]


			if (! defined('DISALLOW_FILE_EDIT')) define('DISALLOW_FILE_EDIT', true);														// [vii]
		}





		/**
		 * [c]	Remove certain links from the admin menu
		 *
		 * 		There are some admin menu links that are redundant - let's hide them.
		 *
		 * 		[i]		Hide the link manager section - it's deprecated in WP anyhow
		 */
		
		public function mangui_startr_hide_pages() {
			remove_menu_page('link-manager.php');	// [i]
		}





		/**
		 * [d]	Prevent WordPress from adding links to inserted media by default
		 *
		 * 		This function resets the default option for inserted media items
		 * 		so that no link is applied.
		 */
		
		public function mangui_startr_no_image_links() {
			$image_set = get_option('image_default_link_type');
			if ($image_set !== 'none') :
				update_option('image_default_link_type', 'none');
			endif;
		}





		/**
		 * [e]	Enqueue our styles and scripts
		 *
		 * 		[i]		@js 	plugins.min.js 		Include this theme's plugins JS files (concat and minified).
		 * 		[ii]	@js 	global.min.js 		Include this theme's custom JS files (concat and minified).
		 * 		[iii]	@css 	screen.css 			Include this theme's main stylesheet (concat and minified).
		 */
		
		public function mangui_startr_enqueue() {
			wp_enqueue_script('mangui__global--plugins', get_stylesheet_directory_uri().'/resources/js/compiled/plugins.min.js', array('jquery')); 	// [i]
			wp_enqueue_script('mangui__global--scripts', get_stylesheet_directory_uri().'/resources/js/compiled/global.min.js', array('jquery')); 	// [ii]
			wp_enqueue_style( 'mangui__global--styles',  get_stylesheet_directory_uri().'/resources/css/compiled/screen.css'); 						// [iii]
		}





		/**
		 * [f]	Function to remove jump links from post content
		 */
		
		public function mangui_startr_remove_jump_links($link) {
			$offset = strpos($link, '#more-');
			
			if ($offset) $end = strpos($link, '"',$offset);
			if ($end) $link = substr_replace($link, '', $offset, $end-$offset);


			return $link;
		}





		/**
		 * [g]	Add framework specific classes to inserted images
		 */
		
		public function mangui_startr_filter_image_html($html) {
			$html = str_replace('wp-image', ' o-image  o-image--id', $html);
			$html = str_replace('size-', 'o-image--size-', $html);
			$html = str_replace('align', 'o-image--align-', $html);


			return $html;
		}





		/**
		 * [h]	Function to remove query string
		 */
		
		public function mangui_startr_remove_query_strings($src){
			$parts = explode('?ver', $src);
			return $parts[0];
		}


	} // class definition





/**
 * [3]	Initialise
 */

new ManguiThemeSetup();






endif; // class_exists