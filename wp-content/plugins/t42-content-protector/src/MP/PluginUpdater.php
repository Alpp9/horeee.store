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

use stdClass;

/**
 * SINGLETON: Class used to implement plugin update mechanism.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class PluginUpdater {
	
	/**
	 * URL from where download plugin information.
	 *
	 * @since 1.0.0
	 **/
	public $plugin_info_url = 'https://updates.42theme.com/wp-json/t42-purchase-validator/v1/updates?';

	/**
	 * The one true PluginUpdater.
	 *
	 * @var PluginUpdater
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new PluginUpdater instance.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function __construct() {

		/** Work only in admin area. */
		if ( ! is_admin() ) { return; }

		/** Disable if plugin don't have Envato ID. */
		$plugin_id = EnvatoItem::get_instance()->get_id();
		if ( $plugin_id === 0 ) { return; }

		/** Check do we have new version? */
		add_filter( 'site_transient_update_plugins', [$this, 'update_plugins'] );
		add_filter( 'transient_update_plugins', [$this, 'update_plugins'] );

		/** Show custom update message. */
		add_action( 'in_plugin_update_message-t42-content-protector/t42-content-protector.php', [$this, 'show_plugin_update_message' ], 10, 2 );

		/** Add plugin info to View details popup.  */
		add_filter( 'plugins_api', [$this, 'plugin_info'], 20, 3 );

	}

	/**
	 * Check do we have new version?
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param $update_plugins
	 *
	 * @return mixed
	 **/
	public function update_plugins( $update_plugins ) {
		global $wp_version;

		/** Reset temporary cache. */
		$this->force_check();

		if ( ! is_object( $update_plugins ) ) {
			return $update_plugins;
		}

		if ( ! isset( $update_plugins->response ) || ! is_array( $update_plugins->response ) ) {
			$update_plugins->response = [];
		}

		/** This method runs multiple times, so we use short time cache.  */

		/** Trying to get from cache first. */
		$plugin_info = get_transient( 't42_content_protector_plugin_info' );

		/** Download data from remote host. */
		if ( false === $plugin_info ) {

			/** Check if there's a new version of plugin. */
			$url = $this->plugin_info_url;
			$url .= 'plugin-slug=' . Plugin::get_slug();

			/** If this copy is activated. */
			if ( PluginActivation::get_instance()->is_activated() ) {

				/** Add PID to url. */
				$purchase_code = get_option( 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id() );
				$url .= '&pid=' . $purchase_code;

			}

			$plugin_info = Helper::get_instance()->file_get_contents_curl( $url );

			/** We don't have info about latest plugin version. */
			if ( false === $plugin_info ) {
				return $update_plugins;
			}

			$plugin_info = json_decode( $plugin_info, false );

			/** Set cache for 5 min. */
			set_transient( 't42_content_protector_plugin_info', $plugin_info, 300 ); // 5 min cache.

		}

		/** Wrong JSON. */
		if ( null === $plugin_info ) {
			return $update_plugins;
		}

		/** When Envato return: "Error: Failed to get data for this item". */
		if ( empty( $plugin_info->version ) ) {
			return $update_plugins;
		}

		$current_version = Plugin::get_version();
		$latest_version = $plugin_info->version;

		/** If Latest version is newer, show  update version. */
		if ( version_compare( $latest_version, $current_version ) > 0 ) {

			/** Download URL only for activated users. */
			$package = '';
			if ( ! empty( $plugin_info->download_url ) ) {
				$package = $plugin_info->download_url;
			}

			$update_plugins->response['t42-content-protector/t42-content-protector.php'] = (object)[
				'slug'          => 't42-content-protector',
				'plugin'        => 't42-content-protector/t42-content-protector.php',
				'new_version'   => $latest_version, // The newest version
				'package'       => $package,
				'tested'        => $wp_version,
				'icons'         => [
					'2x' => Plugin::get_url() . 'images/logo-color.svg',
					'1x' => Plugin::get_url() . 'images/logo-color.svg',
				]
			];

		}

		return $update_plugins;

	}

	/**
	 * Prepare plugin info.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param $res
	 * @param $action
	 * @param $args
	 *
	 * @return bool|stdClass
	 **/
	public function plugin_info( $res, $action, $args ) {

		global $wp_version;

		/** @noinspection PhpUnusedLocalVariableInspection */
		$tmp = $res;

		/** Reset temporary cache. */
		$this->force_check();

		/** Do nothing if this is not about getting plugin information. */
		if ( $action !== 'plugin_information' ) { return false; }

		/** Do nothing if it is not our plugin. */
		if ( 't42-content-protector' !== $args->slug ) { return false; }
		
		/** Trying to get from cache first. */
		$remote = get_transient( 't42_content_protector_upgrade' );

		/** Download plugin JSON, if cache is empty. */
		if ( false === $remote ) {

			$url = $this->plugin_info_url;
			$url .= 'plugin-slug=' . Plugin::get_slug();

			/** If this copy is activated. */
			if ( PluginActivation::get_instance()->is_activated() ) {
				/** Add PID to url. */
				$purchase_code = get_option( 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id() );
				$url .= '&pid=' . $purchase_code;
			}

			/** info.json is the file with the actual plugin information on our server. */
			$remote = wp_remote_get( $url,
				[
					'timeout' => 20,
					'headers' => [
						'Accept' => 'application/json'
					]
				]
			);

			/** Write data to transient. */
			/** @noinspection TypeUnsafeComparisonInspection */
			if (
				! is_wp_error( $remote ) &&
	            isset( $remote['response']['code'] ) &&
		        $remote['response']['code'] == 200 &&
		        ! empty( $remote['body']
	        ) ) {
				set_transient( 't42_content_protector_upgrade', $remote, 5 * HOUR_IN_SECONDS ); // 1 hour cache.
			}

		}

		if ( $remote['body'] ) {

			$remote = json_decode( $remote['body'], false );

			/** Rate limit exceeded. */
			if ( ! empty( $remote->code ) ) { return false; }

			$res = new stdClass();

			$res->name = $remote->name; // Plugin name.
			$res->slug = $remote->slug; // Slug.
			$res->version = $remote->version; // Plugin version.

			$res->last_updated = $remote->last_updated;
			$res->active_installs = $remote->active_installs;

			/** Rating. */
			if( ! empty( $remote->rating ) ) {
				$res->rating = $remote->rating;
				$res->num_ratings = $remote->num_ratings;
			}

			$res->tested = $wp_version; // Tested up to WordPress version.
			$res->requires = $remote->requires; // Requires at least WordPress version.
			$res->requires_php = $remote->requires_php; // The minimum required version of PHP.

			$res->author = '<a href="' . esc_url( $remote->author_homepage ) . '" target="_blank">' . esc_html( $remote->author ) . '</a>';

			/** Prepare contributors. */
			if( ! empty( $remote->contributors ) ) {
				$contributors = [];
				foreach ( $remote->contributors as $contributor ) {
					$contributors[] = get_object_vars( $contributor );
				}

				$res->contributors = $contributors;
			}

			$res->homepage = $remote->homepage;

			/** Download url returned only for valid PID.  */
			if( ! empty( $remote->download_url ) ) {
				$res->download_link = $remote->download_url;
				$res->trunk = $remote->download_url;
			}

			$res->sections = [
				'description' => $remote->sections->description,
				'installation' => $remote->sections->installation,
				'changelog' => $remote->sections->changelog,
			];

			/** FAQ section. */
			if( ! empty( $remote->sections->faq ) ) {
				$res->sections['faq'] = $remote->sections->faq;
			}

			/** Reviews section. */
			if( ! empty( $remote->sections->reviews ) ) {
				$res->sections['ratings_and_reviews'] = $remote->sections->reviews;
			}

			/** Screenshots section. */
			if( ! empty( $remote->sections->screenshots ) ) {
				$res->sections['screenshots'] = $remote->sections->screenshots;
			}

			/** Banners. */
			if( ! empty( $remote->banners ) ) {
				$banners = [];
				foreach ( $remote->banners as $key => $banner ) {
					$banners[$key] = $banner;
				}

				$res->banners = $banners;
			}

			return $res;

		}

		return false;

	}

	/**
	 * Reset temporary options on Force update check.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function force_check() {

		if ( isset( $_GET['force-check'] ) && $_GET['force-check'] === '1' ) {
			$this->reset_cache();
		}

	}

	/**
	 * Reset plugin update cache.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function reset_cache() {

		delete_transient( 't42_content_protector_upgrade' );
		delete_transient( 't42_content_protector_plugin_info' );

	}

	/**
	 * Show custom update messages on plugins page.		
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function show_plugin_update_message() {

		/** Message for non activated users. */
		if ( ! PluginActivation::get_instance()->is_activated() ) {
			echo '<br /><span class="t42-line">&nbsp;</span>';
			esc_attr_e( 'Please activate your copy to enable automatic updates or visit the plugin page on the Envato market to ', 't42-content-protector' );
			echo ' <a href="' . esc_url( EnvatoItem::get_instance()->get_url() ) . '" target="_blank">';
			esc_attr_e( 'download ', 't42-content-protector' );
			echo '</a> ';
			esc_attr_e( 'the latest version.', 't42-content-protector' );
		}

	}

	/**
	 * Main PluginUpdater Instance.
	 *
	 * Insures that only one instance of PluginUpdater exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return PluginUpdater|false
	 **/
	public static function get_instance() {

		/** Work only in admin area. */
		if ( ! is_admin() ) { return false; }

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

} // End Class PluginUpdater.
