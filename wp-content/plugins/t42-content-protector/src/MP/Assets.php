<?php
/**
 * Content Protector for WordPress. Exclusively on Envato Market: https://1.envato.market/42themeCC
 * @encoding     UTF-8
 * @version      1.0.9
 * @copyright    Copyright (C) 2016 - 2021 42Theme (https://42theme.com). All rights reserved.
 * @license      Envato Standard Licenses
 * @author       Alexander Khmelnitskiy
 * @support      support@42theme.com
 **/

namespace T42\ContentProtector;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * Base methods for AdminScripts and AdminStyles.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
 abstract class Assets {

	/**
	 * Return handle for wp_enqueue_ functions.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $name  - Required. Name of the file.
	 * @param bool   $MP    - Optional. Defines the folder from where to enqueue the script.
	 *                        false - /wp-content/plugins/{plugin-name}/js/
	 *                        true - /wp-content/plugins/{plugin-name}/src/MP/assets/js/
	 *                        Default 'false'.
	 *
	 * @return string
	 **/
	public function get_handle( $name, $MP = false ) {

		/** Prepare prefix for handle.  */
		$prefix = 't42-content-protector';

		/** Build handle. */
		$handle = $prefix . '-';

		/** Add -mp- prefix if script is from MP core. */
		if ( $MP ) {
			$handle .= 'mp-';
		}

		$handle .= $name;

		return $handle;

	}

	 /**
	  * Get relative path to script or style folder.
	  *
	  * @since 1.0.0
	  * @access protected
	  *
	  * @param string $type - Required. Folder type: css|js
	  * @param bool   $MP   - Optional. Defines the folder from where to enqueue the script.
	  *                       false - /wp-content/plugins/{plugin-name}/js|css/
	  *                       true - /wp-content/plugins/{plugin-name}/src/MP/assets/js|css/
	  *                       Default 'false'.
	  *
	  * @return string
	  **/
	 protected function get_folder( $type, $MP = false ) {

		 $folder = $type . '/';

		 if ( $MP ) {
			 $folder = 'src/MP/assets/' . $type . '/';
		 }

		 return $folder;

	 }

	 /**
	  * MP wrapper around wp_enqueue_script().
	  *
	  * @since 1.0.0
	  * @access public
	  *
	  * @param string $script_name - Required. Name of the script file.
	  * @param bool   $MP          - Optional. Defines the folder from where to enqueue the script.
	  *                              false - /wp-content/plugins/{plugin-name}/js/
	  *                              true - /wp-content/plugins/{plugin-name}/src/MP/assets/js/
	  *                              Default 'false'.
	  * @param string[] $deps      - Optional. An array of registered script handles this script depends on. Default empty array.
	  * @param bool   $in_footer   - Optional. Whether to enqueue the script before </body> instead of in the <head>.
	  *                        Default 'true'.
	  *
	  * @return void
	  **/
	 public function enqueue_script( $script_name, $MP = false, $deps = [], $in_footer = true ) {

		 /** Exit, if no script. */
		 if ( empty( $script_name ) ) { return; }

		 /** Get relative path to script folder. */
		 $script_folder = $this->get_folder( 'js', $MP );

		 /** Prepare script src. */
		 $src = Plugin::get_url() . $script_folder . $script_name . Plugin::get_suffix() . '.js';

		 /** Enqueue a script. */
		 wp_enqueue_script(
			 $this->get_handle( $script_name, $MP ),
			 $src,
			 $deps,
			 ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? time() : Plugin::get_version(), // Break through caching.
			 $in_footer
		 );

	 }

	 /**
	  * MP wrapper around wp_enqueue_style().
	  *
	  * @since 1.0.0
	  * @access public
	  *
	  * @param string   $css_name    - Required. Name of the css file.
	  * @param bool     $MP          - Optional. Defines the folder from where to enqueue the script.
	  *                                false - /wp-content/plugins/{plugin-name}/css/
	  *                                true - /wp-content/plugins/{plugin-name}/src/MP/assets/css/
	  *                                Default 'false'.
	  * @param string[] $deps        - Optional. An array of registered script handles this script depends on. Default empty array.
	  *
	  * @return void
	  **/
	 public function enqueue_style( $css_name, $MP = false, $deps = [] ) {

		 /** Exit, if no css. */
		 if ( empty( $css_name ) ) { return; }

		 /** Get relative path to script folder. */
		 $css_folder = $this->get_folder( 'css', $MP );

		 /** Prepare script src. */
		 $src = Plugin::get_url() . $css_folder . $css_name . Plugin::get_suffix() . '.css';

		 /** Enqueue a style. */
		 wp_enqueue_style(
			 $this->get_handle( $css_name, $MP ),
			 $src,
			 $deps,
			 ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? time() : Plugin::get_version() // Break through caching.
		 );

	 }

} // End abstract class Assets.
