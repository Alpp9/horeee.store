<?php
/**
 * Content Protector for WordPress. Exclusively on Envato Market: https://1.envato.market/42themeCC
 * @encoding     UTF-8
 * @version      1.0.9
 * @copyright    Copyright (C) 2016 - 2021 42Theme (https://42theme.com). All rights reserved.
 * @license      Envato Standard Licenses
 * @author       Alexander Khmelnitskiy
 * @support      support@42theme.com
 *
 * @wordpress-plugin
 * Plugin Name: Content Protector
 * Plugin URI: https://content-protector-wordpress.42theme.com
 * Description: Prevent Your Content from Being Copied.
 * Version: 1.0.9
 * Requires at least: 5.2
 * Requires PHP: 5.6
 * Author: 42Theme
 * Author URI: https://42theme.com/
 * License: Envato Standard Licenses
 * License URI: https://1.envato.market/license-cc
 * Text Domain: t42-content-protector
 * Domain Path: /languages
 * Tested up to: 5.8
 **/

namespace T42;

use T42\ContentProtector\Config;
use T42\ContentProtector\Sense;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/** Exit if the user does not have a sufficient version of PHP. */
if ( PHP_VERSION_ID < 50600 ) {

    /* translators: %s: admin url */
    wp_die( sprintf( esc_html__( 'Content Protector requires PHP 5.6 or higher. You\'re still on PHP %s. Please contact your web host for more information on how to upgrade your PHP version.', 't42-content-protector' ), PHP_VERSION ) );

}

/** Include plugin autoloader for additional classes. */
require __DIR__ . '/src/autoload.php';

/**
 * SINGLETON: Core class used to implement plugin.
 *
 * This is used to define core logic of plugin.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class ContentProtector {

	/**
	 * The one true ContentProtector.
	 *
	 * @var ContentProtector
	 * @since 1.0.0
	 * @access private
     * @static
	 **/
	private static $instance;

	/**
	 * Sets up a new plugin instance.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function __construct() {

        /** Fire a hook before plugin class setup. */
        do_action( 't42-content-protector_pre_init' );

        /** Initialize Plugin Core and Main variables. */
		MP::get_instance();

	}

	/**
	 * Setup the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function setup() {

		/** Do critical compatibility checks and stop work if it fails. */
		if ( ! MP::get_instance()->initial_checks() ) { return; }

		/** Prepare custom plugin settings. */
		Config::get_instance()->prepare_settings();

		/** Setup the MP. */
		MP::get_instance()->setup();

		/** Custom setups for plugin. */
		Sense::get_instance()->setup();

	}

	/**
	 * Called when a plugin is activated.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public static function on_activation() {

		/** MP on plugin activation.  */
		MP::on_activation();

	}

	/**
	 * Called when a plugin is deactivated.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public static function on_deactivation() {

		/** MP on plugin deactivation.  */
		MP::on_deactivation();

	}

	/**
	 * Main Plugin Instance.
	 *
	 * Insures that only one instance of ContentProtector exists in memory at any one time.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 *
	 * @return ContentProtector
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

} // End class ContentProtector.

/** Run 'on_activation' when the plugin is activated. */
register_activation_hook( __FILE__, [ ContentProtector::class, 'on_activation' ] );

/** Run 'on_deactivation' when the plugin is deactivated. */
register_deactivation_hook( __FILE__, [ ContentProtector::class, 'on_deactivation' ] );

/** Run Plugin class once after activated plugins have loaded. */
add_action( 'plugins_loaded', [ ContentProtector::get_instance(), 'setup' ] );
