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

use T42\ContentProtector\Cache;
use T42\ContentProtector\Helper;
use T42\ContentProtector\EnvatoItem;
use T42\ContentProtector\Plugin;
use T42\ContentProtector\Settings;

/** Exit if uninstall.php is not called by WordPress. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement uninstall operations for plugin.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class UninstallHelper {

	/**
	 * The one true UninstallHelper.
	 *
	 * @var UninstallHelper
	 * @since 1.0.0
	 * @access private
	 **/
	private static $instance;

	/**
	 * Sets up a new Uninstall instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Get Uninstall mode. */
		$mode = $this->get_mode();

		/** Send uninstall Action to our host. */
		Helper::get_instance()->send_action( 'uninstall', 't42-content-protector', '1.0.9' );

		/** Remove Plugin and Settings. */
		if ( 'plugin+settings' === $mode ) {

			/** Remove Plugin Settings. */
			$this->remove_settings();

		}

		/** Remove Plugin, Settings and all created data. */
		elseif ( 'plugin+settings+data' === $mode ) {

			/** Remove Plugin Settings. */
			$this->remove_settings();

			/** Remove Plugin Data. */
			$this->remove_data();

		}

	}

	/**
	 * Return uninstall mode.
	 *
	 * plugin - Will remove the plugin only. Settings and created files will be saved. Used when updating the plugin.
	 * plugin+settings - Will remove the plugin and settings. Created files will be saved. As a result, all settings will be set to default values. Like after the first installation.
	 * plugin+settings+data - Full Removal. This option will remove the plugin with settings and all created files. Use only if you are sure what you are doing.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 **/
	public function get_mode() {

		/** Get plugin uninstall mode. */
		return Settings::get_instance()->options['delete_plugin'];

	}

	/**
	 * Delete all Plugin Options.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function remove_settings() {

		$snake_slug = Plugin::get_snake_slug();

		/** Delete options by prefix. */
		$this->delete_options_with_prefix( $snake_slug );

		/** Delete transients by prefix. */
		$this->delete_transients_with_prefix( $snake_slug );

		/** Remove cache table. */
		$cache = new Cache();
		$cache->drop_cache_table();

	}

	/**
	 * Delete all options from the database whose keys have a specific prefix.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $prefix The prefix.
	 *
	 * @return void
	 **/
	private function delete_options_with_prefix( $prefix ) {

		/** Prepare array with options to remove. */
		$settings = [];

		foreach ( wp_load_alloptions() as $option => $value ) {

			if ( strpos( $option, $prefix ) === 0 ) {

				$settings[] = $option;

			}

		}

		/** Special case for Envato purchase code. */
		$settings[] = 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id();

		/** Remove options for Multisite. */
		if ( is_multisite() ) {

			foreach ( $settings as $key ) {

				if ( ! get_site_option( $key ) ) { continue; }

				delete_site_option( $key );

			}

		}

		/** Remove options for Singular site. */
		else {

			foreach ( $settings as $key ) {

				if ( ! get_option( $key ) ) { continue; }

				delete_option( $key );

			}

		}

	}

	/**
	 * Delete all transients from the database whose keys have a specific prefix.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $prefix The prefix.
	 *
	 * @return void
	 **/
	private function delete_transients_with_prefix( $prefix ) {

		/** Remove transients for Multisite. */
		if ( is_multisite() ) {

			foreach ( $this->get_transient_keys_with_prefix( $prefix ) as $key ) {

				delete_site_transient( $key );

			}

		}

		/** Remove transients for Singular site. */
		else {

			foreach ( $this->get_transient_keys_with_prefix( $prefix ) as $key ) {

				delete_transient( $key );

			}

		}

	}

	/**
	 * Gets all transient keys in the database with a specific prefix.
	 *
	 * Note that this doesn't work for sites that use a persistent object
	 * cache, since in that case, transients are stored in memory.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  string $prefix Prefix to search for.
	 * @return array          Transient keys with prefix, or empty array on error.
	 *
	 * @return array
	 **/
	private function get_transient_keys_with_prefix( $prefix ) {

		global $wpdb;

		$prefix = $wpdb->esc_like( '_transient_' . $prefix );

		/** @noinspection SqlDialectInspection */
		/** @noinspection SqlNoDataSourceInspection */
		$sql  = "SELECT `option_name` FROM $wpdb->options WHERE `option_name` LIKE '%s'";
		$keys = $wpdb->get_results( $wpdb->prepare( $sql, $prefix . '%' ), ARRAY_A );

		if ( is_wp_error( $keys ) ) { return []; }

		return array_map( static function( $key ) {

			/** Remove '_transient_' from the option name. */
			return substr( $key['option_name'], strlen( '_transient_' ) );

		}, $keys );

	}

	/**
	 * Delete all data created by plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function remove_data() {

		/** Remove all tables started with plugin slug. */
		$this->remove_tables();

		/** Remove folder with slug name from uploads. */
		$this->remove_folder();

	}

	/**
	 * Remove all tables started with plugin slug.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function remove_tables() {

		global $wpdb;

		$tables = $wpdb->tables();

		foreach ( $tables as $table ) {

			/** Get plugin snake slug. */
			$table_slug = Plugin::get_snake_slug();

			/** If table name starts with prefix_plugin_slug */
			if ( strpos( $table, $wpdb->prefix . $table_slug ) === 0 ) {

				/** Remove this table. */
				/** @noinspection SqlNoDataSourceInspection */
				$wpdb->query( "DROP TABLE IF EXISTS $table" );

			}

		}

	}

	/**
	 * Remove folder with slug name from uploads.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function remove_folder() {

		/** Remove /wp-content/uploads/plugin-slug/ folder. */
		$dir = trailingslashit( wp_upload_dir()['basedir'] ) . Plugin::get_slug();

		if ( is_dir( $dir ) ) {
			Helper::get_instance()->remove_directory( $dir );
		}

	}

	/**
	 * Main Uninstall Instance.
	 *
	 * Insures that only one instance of Uninstall exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return UninstallHelper
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

} // End class UninstallHelper.
