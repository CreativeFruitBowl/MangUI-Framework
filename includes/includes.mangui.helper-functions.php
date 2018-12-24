<?php

/**
 * [MangUI]	API Helpers
 *
 * These are helper functions for the plugin
 *
 * @category    api
 * @package     mangui
 * @author      Andi North <andi@mangopear.co.uk>
 * @copyright  	2018 Mangopear creative
 * @license     GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @since       1.1.0
 * @version     1.1.0
 */


/**
 * CHANGELOG
 *
 * @version 1.1.0
 *          Init
 */


/**
 * CONTENTS
 *
 * [1]	mangui_get_setting
 * [2]	mangui_maybe_get
 * [3]	mangui_get_compatibility
 * [4]	mangui_update_setting
 * [5]	mangui_append_setting
 * [6]	mangui_has_done
 * [7]	mangui_get_path
 * [8]	mangui_get_dir
 * [9]	mangui_include
 * [10]	mangui_parse_args
 * [11]	mangui_parse_types
 * [12]	mangui_parse_type
 * [13]	mangui_get_view
 * [14]	mangui_is_author_viewing
 */


/**
 * [1]	mangui_get_setting
 *
 * 		[a]	Get vars
 * 		[b]	Find setting
 * 		[c]	Filter for third party customisation
 * 		[d]	Return
 */

function mangui_get_setting($name, $default = null) {
	$settings = MangUI()->settings;										// [a]
	$setting = mangui_maybe_get($settings, $name, $default);			// [b]
	$setting = apply_filters("mangui/settings/{$name}", $setting);		// [c]
	return $setting;													// [d]
}





/**
 * [2]	mangui_maybe_get
 *
 * 		A function that will return a var if it exists in an array
 *
 * 		@since  1.1.0
 *
 * 		[a]	Get vars
 * 		[b]	Loop through keys
 * 		[c]	Return default if does not exist
 * 		[d]	Update $array
 * 		[e]	Return value
 */

function mangui_maybe_get($array, $key, $default = null) {
	$keys = explode('/', $key);										// [a]


	foreach ($keys as $k) :											// [b]
		if (! isset($array[$k])) return $default;					// [c]
		$array = $array[$k];										// [d]
	endforeach;


	return $array;													// [e]
}





/**
 * [3]	mangui_get_compatibility
 *
 * 		This function will return true or false for a given compatibility setting
 *
 * 		@since  1.1.0
 */

function mangui_get_compatibility($name) {
	return apply_filters("mangui/compatibility/{$name}", false);
}





/**
 * [4]	mangui_update_setting
 *
 * 		This function will update a value into the settings array found in the mangui object
 *
 * 		@since  1.1.0
 */

function mangui_update_setting($name, $value) {
	MangUI()->settings[$name] = $value;
}





/**
 * [5]	mangui_append_setting
 *
 * 		This function will update a value into the settings array found in the MangUI object
 *
 * 		@since  1.1.0
 *
 * 		[a]	Create array if needed
 * 		[b]	Append to array
 */

function mangui_append_setting($name, $value) {
	if (! isset(MangUI()->settings[$name])) :			// [a]
		MangUI()->settings[$name] = array();
	endif;
	
	
	MangUI()->settings[$name][] = $value;				// [b]
}





/**
 * [6]	mangui_has_done
 *
 * 		This function will return true if this action has already been done
 *
 * 		@since  1.1.0
 *
 * 		[a]	Vars
 * 		[b]	Return true if already done
 * 		[c]	Update setting
 * 		[d]	Return value
 */

function mangui_has_done($name) {
	$setting = 'has_done_' . $name;						// [a]
	if (mangui_get_setting($setting)) return true;		// [b]
	mangui_update_setting($setting, true);				// [c]
	return false;										// [d]
}





/**
 * [7]	mangui_get_path
 *
 * 		This function will return the path to a file within the mangui plugin folder
 *
 * 		@since  1.1.0
 */

function mangui_get_path($path) {
	return mangui_get_setting('path') . $path;
}





/**
 * [8]	mangui_get_dir
 *
 * 		This function will return the url to a file within the mangui plugin folder
 *
 * 		@since  1.1.0
 */

function mangui_get_dir($path) {
	return mangui_get_setting('dir') . $path;
}





/**
 * [9]	mangui_include
 *
 * 		This function will return the url to a file within the mangui plugin folder
 *
 * 		@since  1.1.0
 */

function mangui_include($file) {
	$path = mangui_get_dir($file);
	if (file_exists($path)) include_once($path);
}





/**
 * [10]	mangui_parse_args
 *
 * 		This function will merge together 2 arrays and also convert any numeric values to ints
 *
 * 		@since  1.1.0
 *
 * 		[a]	$args may not be an array!
 * 		[b]	Parse args
 * 		[c]	Parse types
 * 		[d]	Return values
 */

function mangui_parse_args($args, $defaults = array()) {
	if (! is_array($args)) $args = array();					// [a]
	$args = wp_parse_args($args, $defaults);				// [b]
	$args = mangui_parse_types($args);						// [c]
	return $args;											// [d]
}





/**
 * [11]	mangui_parse_types
 *
 * 		This function will convert any numeric values to int and trim strings
 *
 * 		@since  1.1.0
 *
 * 		[a]	Some keys are restricted
 * 		[b]	Loop
 * 		[c]	Parse type if not restricted
 * 		[d]	Return values
 */

function mangui_parse_types($array) {
	$restricted = array(																		// [a]
		'label',
		'name',
		'value',
		'instructions',
		'nonce'
	);
	
	
	foreach( array_keys($array) as $k ) :														// [b]
		if (! in_array($k, $restricted, true)) $array[$k] = mangui_parse_type($array[$k]);		// [c]
	endforeach;
	

	return $array;																				// [d]
}





/**
 * [12]	mangui_parse_type
 *
 * 		@since  1.1.0
 *
 * 		[a]	Test for array
 * 		[b]	Bail early if not string
 * 		[c]	Trim
 * 		[d]	Numbers
 * 		[e]	Return values
 */

function mangui_parse_type($v) {
	if (is_array($v)) return mangui_parse_types($v);				// [a]
	if (! is_string($v)) return $v;									// [b]
	$v = trim($v);													// [c]
	if (is_numeric($v) && strval((int)$v) === $v) $v = intval($v);	// [d]
	return $v;														// [e]
}





/**
 * [13]	mangui_get_view
 *
 *		This function will load in a file from the 'admin/views' folder and allow variables to be passed through
 *
 * 		@since  1.1.0
 */

function mangui_get_view($view_name = '', $args = array()) {
	$path = mangui_get_path("admin/views/{$view_name}.php");	
	if (file_exists($path)) include($path);
}





/**
 * [14]	mangui_is_author_viewing
 *
 * 		Is the author of the post the user viewing the post?
 *
 * 		@since  1.3.0
 */

function mangui_is_author_viewing($user_id = '', $post_id = '') {
	$get_user_id = ($user_id) ? $user : get_current_user_id();
	$get_post_id = ($post_id) ? $post_id : get_the_ID();
	$author_uid  = get_post_field('post_author', $get_post_id);


	if ($get_user_id == $author_uid) :
		return true;
	else :
		return false;
	endif;
}

?>