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

use WP_Screen;
use WP_Scripts;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * Adds JavaScripts to admin area.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class AdminScripts extends Assets {

	/**
	 * The one true AdminScripts.
	 *
	 * @since 1.0.0
	 * @var AdminScripts
	 **/
	private static $instance;

	/**
	 * Sets up a new AdminScripts instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	private function __construct() {

		/** JavaScrips for admin area. */
		add_action( 'admin_enqueue_scripts', [$this, 'admin_scripts'] );

		/** Remove jQuery Migrate. */
		add_action( 'wp_default_scripts', [ $this, 'remove_jquery_migrate' ] );

	}

	/**
	 * Add JavaScrips for admin area.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function admin_scripts() {

		/** Get current screen object. */
		$screen = get_current_screen();
		if ( null === $screen ) { return; }

	    /** Add scripts on Plugin Settings page. */
		$this->settings_scripts( $screen );

	}

	/**
	 * Add scripts on Plugin Settings page.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param WP_Screen $screen - Current screen object.
	 *
	 * @return void
	 **/
	private function settings_scripts( $screen ) {

		/** Work only on plugin settings page. */
		if ( ! in_array( $screen->base, Plugin::get_menu_bases(), true ) ) { return; }

		/** For development and debug. */
		if  ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {

			/** MP UIkit JS. */
			$this->enqueue_script( 'uikit', true );

			/** MP uikit-icons.js */
			$this->enqueue_script( 'uikit-icons', true );

			/** MP iziToast.js */
			$this->enqueue_script( 'iziToast', true );

			/** MP smoothscroll.js */
			$this->enqueue_script( 'smoothscroll', true );

			/** MP ion.rangeSlider.js */
			$this->enqueue_script( 'ion.rangeSlider', true, ['jquery'] );

			/** MP select2.js */
			$this->enqueue_script( 'select2', true, ['jquery'] );

			/** MP popper.js */
			$this->enqueue_script( 'popper', true );

			/** MP tippy.js */
			$this->enqueue_script( 'tippy', true );

			/** MP spectrum.js */
			$this->enqueue_script( 'spectrum', true, ['jquery'] );

			/** MP gpickr.js */
			$this->enqueue_script( 'gpickr', true );

			/** MP iconpicker.js */
			$this->enqueue_script( 'iconpicker', true );

			/** MP jquery.form.js */
			$this->enqueue_script( 'jquery.form', true, ['jquery'] );

			/** MP color-scheme.js */
			$this->enqueue_script( 'color-scheme', true );

			/** MP jquery.bootstrap-touchspin.js */
			$this->enqueue_script( 'jquery.bootstrap-touchspin', true );

			/** MP MP ripple.js */
			$this->enqueue_script( 'ripple', true );

		}

		/** MP admin.js. */
		$this->enqueue_script( 'admin', true, ['jquery'] );

		/** Prepare values to pass to JS. */
		$to_js = [
			'ajaxURL'   => admin_url( 'admin-ajax.php' ),
			'pluginURL' => Plugin::get_url(),
			'nonce'     => wp_create_nonce( 't42-content-protector' ), // Nonce for security.
		];
        wp_localize_script( $this->get_handle( 'admin', true ), 't42ContentProtector', $to_js );

		/** admin.js. */
		$this->enqueue_script( 'admin', false, ['jquery'] );

	}

	/**
	 * Remove jQuery Migrate script from the jQuery bundle only in front end.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WP_Scripts $scripts WP_Scripts object.
	 *
	 * @return void
	 **/
	public function remove_jquery_migrate( $scripts ) {

		/** Exit if no jQuery. */
		if ( ! isset( $scripts->registered[ 'jquery' ] ) ) { return; }

		$script = $scripts->registered['jquery'];

		/** Check whether the script has any dependencies. */
		if ( $script->deps ) {
			$script->deps = array_diff( $script->deps, [ 'jquery-migrate' ] );
		}

	}

	/**
	 * Main AdminScripts Instance.
	 * Insures that only one instance of AdminScripts exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return AdminScripts
	 **/
	public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function __clone() {

		/** Cloning instances of the class is forbidden. */
		_doing_it_wrong( __FUNCTION__, esc_html__( 'The whole idea of the singleton design pattern is that there is a single object therefore, we don\'t want the object to be cloned.', 't42-content-protector' ), '1.0.9' );

	}

	/**
	 * Disable unserializing of the class.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be unserialized.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function __wakeup() {

		/** Unserializing instances of the class is forbidden. */
		_doing_it_wrong( __FUNCTION__, esc_html__( 'The whole idea of the singleton design pattern is that there is a single object therefore, we don\'t want the object to be unserialized.', 't42-content-protector' ), '1.0.9' );

	}

} // End class AdminScripts.
