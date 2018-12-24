<?php

/**
 * [MangUI]	Core functionality
 *
 * This class contains the core MangUI functionality - including requesting other
 * files to enhance basic WordPress
 *
 * @package     mangui
 * @author      Andi North <andi@mangopear.co.uk>
 * @copyright  	2018 Mangopear creative
 * @license     GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @since       1.0.0
 * @version     1.0.0
 */


/**
 * CHANGELOG
 *
 * @version 1.0.0
 *          Init
 */


/**
 * CONTENTS
 *
 * [1]  Forbid direct loading of this file
 * [2]	Define class
 * [3]	Launch the whole plugin
 * [4]	Initialise
 */


/**
 * [1]	Forbid direct loading of this file
 */

if (! defined('ABSPATH')) { exit; }





/**
 * [2]	Define class
 *
 * 		[a]	Dummy constructor
 * 		[b]	Initialise
 * 		[c]	Init
 * 		[d]	Register post types
 * 		[e]	Register assets
 *   	[f]	Register navigations
 *   	[g]	Register default widgets
 *   	[h]	Register default sidebars
 *   	[i]	Prevent WordPress from adding links to images by default
 *   	[j]	Add framework specific classes to images
 */

if (! class_exists('MangUI')) :
	class MangUI {


		/**
		 * [a]	Dummy constructor
		 *
		 * 		A dummy constructor to ensure MangUI is only initialised once.
		 *
		 * 		@since  1.3.0
		 */

		public function __construct() {
			// Do nothing!
		}





		/**
		 * [b]	Initialise
		 *
		 * 		The real constructor to initialise MangUI.
		 *
		 * 		@since  1.3.0
		 *
		 * 		[i] 	Define our settings
		 * 		[ii]	Register actions
		 * 		[iii]	Register new navigations
		 * 		[iv]	Register default widgets
		 * 		[v]		Allow shortcodes to be executed within widgets
		 * 		[vi]	Change defaults to stop images having links by default
		 * 		[vii]	Filter image markup to add custom classes
		 * 		[xxx]	Include helpers
		 */

		public function initialize() {

			/**
			 * [i] 	Define our settings
			 *
			 * 		[a]	Basic settings
			 * 		[b]	URLs
			 * 		[c]	Options
			 */

			$this->settings = array(
				'name'				=>	__('MangUI', 'mangui'),								// [a]
				'version'			=>	'5.0.0',											// [a]

				'path'				=>  get_stylesheet_directory(),							// [b]
				'dir'				=>  get_stylesheet_directory_uri(),						// [b]

				'show_admin'		=>	true,												// [c]
				'show_updates'		=> 	true,												// [c]
				'stripslashes'		=> 	false,												// [c]
				'local'				=> 	true,												// [c]
				'json'				=> 	true,												// [c]
				'save_json'			=> 	'',													// [c]
				'load_json'			=> 	array(),											// [c]
				'default_language'	=> 	'en-GB',											// [c]
				'current_language'	=> 	'en-GB',											// [c]
				'l10n'				=> 	false,												// [c]
				'l10n_textdomain'	=> 	'mangui'											// [c]
			);


			add_action('init', array($this, 'init'), 5);									// [ii]
			add_action('init', array($this, 'register_assets'), 5);							// [ii]
			add_action('init', array($this, 'register_navigations'), 5);					// [iii]
			add_action('init', array($this, 'register_default_sidebars'), 5);				// [iv]
			add_filter('widget_text', 'do_shortcode');										// [v]
			add_action('init', array($this, 'no_image_links'), 5);							// [vi]
			add_filter('get_image_tag_class', array($this, 'filter_image_html'), 0, 4);		// [vii]


			include_once('includes.mangui.helper-functions.php');							// [xxx]
		}





		/**
		 * [c]	Init
		 *
		 * 		This function will run after all plugins and theme functions have been included.
		 *
		 * 		@since  1.3.0
		 *
		 * 		[i] 	Bail early if a plugin calls too early
		 * 		[ii]	Bail early if already init
		 * 		[iii]	Only run once
		 * 		[iv]	Redeclare directory
		 * 		[v]		Vars
		 * 		[vi]	Multilingual
		 * 		[vii]	Add action for third parties to hook in to
		 * 		[viii]	Clean up <head> output
		 * 		[ix]	Add default posts and comments RSS feed links to <head> output.
		 * 		[x]		Enable support for post thumbnails on posts and pages
		 * 		[xi]	Enable support for HTML5 markup
		 */

		public function init() {
			if (! did_action('plugins_loaded')) return;																	// [i]
			if (mangui_get_setting('init')) return;																		// [ii]


			mangui_update_setting('init', true);																		// [iii]
			mangui_update_setting('dir', get_stylesheet_directory_uri());												// [iv]


			$major = intval(mangui_get_setting('version'));																// [v]


			load_textdomain('mangui', mangui_get_path( 'languages/mangui-' . get_locale() . '.mo' ));					// [vi]
			do_action('mangui/init');																					// [vii]


			remove_action('wp_head', 'rsd_link');																		// [viii]
			remove_action('wp_head', 'wlwmanifest_link');																// [viii]
			remove_action('wp_head', 'wp_generator');																	// [viii]
			remove_action('wp_head', 'wp_shortlink_wp_head');															// [viii]


			add_theme_support('automatic-feed-links');																	// [ix]
			add_theme_support('post-thumbnails');																		// [x]
			add_theme_support('html5', array('comment-list', 'search-form', 'comment-form', 'gallery', 'caption',));	// [xi]
		}





		/**
		 * [e]	Register assets
		 *
		 * 		Includes to register our post types.
		 *
		 * 		@since  1.3.0
		 *
		 * 		[i] 	@var Get plugin version number
		 * 		[ii] 	@var Get language locale
		 * 		[iii] 	@var Get string for minified scripts
		 * 		[iv]	[script] MangUI core JS
		 * 		[v]		[styles] MangUI core CSS
		 * 		[vi]	[scripts] MangUI admin AJAX
		 * 		[vii]	Register editor styles
		 * 		[viii]	Remove SearchWP styles
		 */

		public function register_assets() {
			$version = mangui_get_setting('version');																						// [i]
			$language = get_locale();																										// [ii]
			$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';																	// [iii]


			if (! is_admin()) :
				wp_enqueue_style('mangui-css', mangui_get_dir('/resources/css/compiled/screen' . $min . '.css');							// [v]
				wp_enqueue_script('mangui-js', mangui_get_dir('/resources/js/compiled/global'  . $min . '.js'), array('jquery'));			// [vi]
				wp_localize_script('mangui-js', 'mangui_ajax', array('ajax_url' => admin_url('admin-ajax.php')));							// [vi]
			endif;


			add_editor_style('/resources/css/compiled/editor' . $min . '.css');																// [vii]
			wp_deregister_style('searchwp-live-search');																					// [viii]
		}





		/**
		 * [f]	Register navigations
		 *
		 * 		Register default navigations
		 *
		 * 		@since  1.0.0
		 *
		 * 		[i] 	Register default header & footer navigations
		 */

		public function register_navigations() {
			register_nav_menus(
				array(
					'main' 	 => __('Main website navigation', 'hildon'), 			// [i]
					'footer' => __('Footer navigation', 'hildon'), 					// [i]
				)
			);
		}





		/**
		 * [h]	Register default sidebars
		 *
		 * 		Register default sidebar
		 *
		 * 		@since  1.0.0
		 *
		 * 		[i] 	Register default sidebar
		 */

		public function register_default_sidebars() {
			register_sidebar(
				array(
					'name'          => __('Default sidebar', 'mangui'),
					'id'            => 'default',
					'description'   => '',
					'before_widget' => '<aside id="%1$s" class="widget %2$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h1 class="widget-title">',
					'after_title'   => '</h1>',
				)
			);
		}





		/**
		 * [i]	Prevent WordPress from adding links to images by default
		 *
		 * 		Register default sidebar
		 *
		 * 		@since  1.0.0
		 *
		 * 		[i] 	Register default sidebar
		 */

		public function no_image_links() {
			$image_set = get_option('image_default_link_type');

			if ($image_set !== 'none') {
				update_option('image_default_link_type', 'none');
			}
		}





		/**
		 * [j]	Add framework specific classes to images
		 *
		 * 		This function amends the classes that are added to images by default
		 *   	when they are inserted into the post content area from the 
		 *    	"Insert Media" modal.
		 *
		 * 		@since  1.0.0
		 *
		 * 		[i] 	Register default sidebar
		 */

		public function filter_image_html() {
			$class = str_replace('wp-image', ' o-image  o-image--id', $class);
			$class = str_replace('size-', 'o-image--size-', $class);
			$class = str_replace('align', 'o-image--align-', $class);

			return $class;
		}


	} // class definition





/**
 * [3]	Launch the whole plugin
 *
 * 		Returns the main instance of MangUI to prevent the use of globals.
 *
 * 		@since  1.3.0
 * 		@return MangUI
 */

function MangUI() {
	global $mangui;


	if (! isset($mangui)) :
		$mangui = new MangUI();
		$mangui->initialize();
	endif;


	return $mangui;
}





/**
 * [4]	Initialise
 */

MangUI();






endif; // class_exists