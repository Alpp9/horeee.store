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

namespace T42;

use T42\ContentProtector\AdminScripts;
use T42\ContentProtector\AdminStyles;
use T42\ContentProtector\EnvatoItem;
use T42\ContentProtector\Helper;
use T42\ContentProtector\Merlin;
use T42\ContentProtector\Plugin;
use T42\ContentProtector\PluginUpdater;
use T42\ContentProtector\Settings;
use T42\ContentProtector\PluginHelper;
use T42\ContentProtector\CheckCompatibility;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Core class used to implement Master Plugin functionality.
 *
 * @since 1.0.0
 **/
final class MP {

	/**
	 * The one true Plugin Instance.
	 *
	 * @var MP
	 * @since 1.0.0
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
		
		/** Initialize main variables. */
		Plugin::get_instance();

	}

	/**
	 * Do critical compatibility checks and stop work if fails.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool
	 **/
	public function initial_checks() {

		/** Do critical initial checks. */
		$result = CheckCompatibility::get_instance()->do_initial_checks( true );

		/** Show warning about failed initial checks. */
		if ( false === $result ) {

			CheckCompatibility::get_instance()->add_failed_notice();

		}

		return $result;

	}

	/**
	 * Setup the MP.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function setup() {

		/** Do critical initial checks. */
		if ( ! CheckCompatibility::get_instance()->do_initial_checks( true ) ) { return; }

		/** Send install Action to our host. */
		self::send_install_action();

		/** Define hooks that runs on both the front-end as well as the dashboard. */
		$this->both_hooks();

		/** Define admin hooks. */
		$this->admin_hooks();

	}

	/**
	 * Define hooks that runs on both the front-end as well as the dashboard.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function both_hooks() {

		/** Load translation. */
		add_action( 'plugins_loaded', [$this, 'load_textdomain'] );

		/** Initialise Merlin WP. */
		$this->initialise_merlin();

	}

	/**
	 * Initialise Merlin WP.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function initialise_merlin() {

		/** Exit if Merlin disabled. */
		if ( ! Plugin::get_config( 'merlin:enabled' ) ) { return; }

		/** Initialise Merlin WP. */
		new Merlin();

	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function admin_hooks() {

		/** Work only in admin area. */
		if ( ! is_admin() ) { return; }

		/** Run PluginHelper. */
		PluginHelper::get_instance();

		/** Create plugin setting page. */
		Settings::get_instance();

		/** Add admin CSS */
		AdminStyles::get_instance();

		/** Add admin JS */
		AdminScripts::get_instance();

		/** Remove "Thank you for creating with WordPress" and WP version only from plugin settings page. */
		add_action( 'admin_enqueue_scripts', [$this, 'remove_wp_copyrights'] );

		/** Remove all "third-party" notices from plugin settings page. */
		add_action( 'in_admin_header', [$this, 'remove_all_notices'], 1000);

		/** Plugin update mechanism enable only if plugin have Envato ID. */
		$plugin_id = EnvatoItem::get_instance()->get_id();
		if ( $plugin_id > 0 ) {
			PluginUpdater::get_instance();
		}

	}

	/**
	 * Remove "Thank you for creating with WordPress" and WP version only from plugin settings page.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	public function remove_wp_copyrights() {

		/** Remove "Thank you for creating with WordPress" and WP version from plugin settings page. */
		$screen = get_current_screen(); // Get current screen.
		if ( null === $screen ) { return; }

		/** Plugin Settings Page. */
		if ( in_array( $screen->base, Plugin::get_menu_bases(), true ) ) {

			add_filter( 'admin_footer_text', '__return_empty_string', 11 );
			add_filter( 'update_footer', '__return_empty_string', 11 );

		}

	}

	/**
	 * Remove all other notices on plugin settings page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function remove_all_notices() {

		/** Work only on plugin settings page. */
		$screen = get_current_screen();
		if ( null === $screen ) { return; }

		/** Remove notices only on plugin settings page. */
		if ( ! in_array( $screen->base, Plugin::get_menu_bases(), true ) ) { return; }

		/** Remove all notices. */
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function load_textdomain() {

		load_plugin_textdomain( Plugin::get_slug(), false, Plugin::get_path() . 'languages/' );

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

		/** Security checks. */
		if ( ! current_user_can( 'activate_plugins' ) ) { return; }

		/** We need to know plugin to activate it. */
		if ( ! isset( $_REQUEST['plugin'] ) ) { return; }

		/** Get plugin. */
		$plugin = filter_var( $_REQUEST['plugin'], FILTER_SANITIZE_STRING );

		/** Checks that a user was referred from admin page with the correct security nonce. */
		check_admin_referer( "activate-plugin_$plugin" );

		/** Do critical initial checks. */
		if ( ! CheckCompatibility::get_instance()->do_initial_checks( false ) ) { return; }

		/** Merlin enabled and not completed. */
		if ( Plugin::get_config('merlin:enabled') && ! get_option( 't42-content-protector_merlin_completed' ) ) {

			/** Enable redirect to installation wizard. */
			set_transient( Plugin::get_slug() . '_merlin_redirect', 1 );

		}

		/** Send install Action to our host. */
		self::send_install_action();

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

	}

	/**
	 * Send install Action to our host.
	 *
	 * @static
	 * @since 1.0.0
	 *
	 * @return void
	 **/
	private static function send_install_action() {

		/** Have we already sent 'install' for this version? */
		$opt_name = Plugin::get_snake_slug() . '_send_action_install';
		$ver_installed = get_option( $opt_name );

		/** Send install Action to our host. */
		if ( ! $ver_installed || Plugin::get_version() !== $ver_installed ) {

			/** Send install Action to our host. */
			Helper::get_instance()->send_action( 'install', Plugin::get_slug(), Plugin::get_version() );
			update_option( $opt_name, Plugin::get_version() );

		}

	}

	/**
	 * Create or Return MP instance.
	 *
	 * Insures that only one instance of MP exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return MP
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

} // End class MP