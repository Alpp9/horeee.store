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

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * Adds CSS styles to admin area.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class AdminStyles extends Assets {

	/**
	 * The one true AdminStyles.
	 *
	 * @since 1.0.0
	 * @var AdminStyles
	 **/
	private static $instance;

	/**
	 * Sets up a new AdminStyles instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		add_action( 'admin_enqueue_scripts', [$this, 'admin_styles'] );

	}

	/**
	 * Add CSS for admin area.
     *
	 * @since 1.0.0
     * @access public
	 *
	 * @return void
	 **/
	public function admin_styles() {

		/** Get current screen object. */
		$screen = get_current_screen();
		if ( null === $screen ) { return; }

		/** Add styles on Plugin Settings Page. */
		$this->settings_styles( $screen );

		/** Plugins page. Styles for "View version details" popup. */
		$this->plugin_update_styles( $screen );

	}

	/**
	 * Add styles on Plugin Settings Page.
     *
	 * @since 1.0.0
     * @access private
	 *
	 * @param WP_Screen $screen - Current screen object.
	 *
	 * @return void
	 **/
	private function settings_styles( $screen ) {

		/** Work only on plugin settings page. */
		if ( ! in_array( $screen->base, Plugin::get_menu_bases(), true ) ) { return; }

		$uikit_css_name = 'uikit';

		/** RTL languages. */
		if ( is_rtl() ) { $uikit_css_name = 'uikit-rtl'; }

		/** MP uikit.css|uikit-rtl.css */
		$this->enqueue_style( $uikit_css_name, true );

		/** MP admin.css */
		$this->enqueue_style( 'admin', true );

	}

	/**
	 * Styles for plugins page. "View version details" popup.
	 *
	 * @since 1.0.0
     * @access private
	 *
	 * @param WP_Screen $screen - Current screen object.
     *
	 * @return void
	 **/
	private function plugin_update_styles( $screen ) {

		/** Work only in "View version details" popup. */
		if ( 'plugin-install' !== $screen->base ) { return; }

		/** Load css only for current plugin. */
		if ( isset( $_GET['plugin'] ) && 't42-content-protector' === $_GET['plugin'] ) {

			/** MP plugin-install.css */
			$this->enqueue_style( 'plugin-install', true );

		}

	}

	/**
	 * Main AdminStyles Instance.
	 * Insures that only one instance of AdminStyles exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return AdminStyles
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

} // End class AdminStyles.
