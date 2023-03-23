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

use stdClass;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class contain information about the envato item.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class EnvatoItem {

	/**
	 * All item details.
	 *
	 * @var stdClass
	 * @since 1.0.0
	 **/
	private static $item;

	/**
	 * The one true EnvatoItem.
	 *
	 * @var EnvatoItem
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new EnvatoItem instance.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function __construct() {

		/** Do we have Item details in cache? */
		$cache = new Cache( 24 * HOUR_IN_SECONDS );
		$key = Plugin::get_slug();
		$cached_item = $cache->get( $key );

		if (
            is_null( $cached_item ) || // If we haven't a cached item OR
            (
                $this->is_empty_cached_item( $cached_item ) && // We have cached item but it's empty AND
                $this->is_settings_page() // This is plugin settings page.
            )
        ) {

			/** Get item details from remote host. */
			$remote_item = $this->get_remote_item();

			/** Store item details in cache. */
			$cache->set( $key, [$key => $remote_item] );

			/** Use remote item details. */
			self::$item = $remote_item;

        }

		/** We have NOT empty cached item. */
		elseif ( ! $this->is_empty_cached_item( $cached_item ) ) {

			/** Use item details from cache. */
			$cached_item_details = json_decode( $cached_item, true );
			self::$item = $cached_item_details[$key];

        }

		/** False for all other cases. */
        else {

			self::$item = false;

        }

	}

	/**
	 * Return true if item details is empty.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return boolean
	 **/
    private function is_empty_cached_item( $cached_item ) {

	    $cached_item_details = json_decode( $cached_item, true );

	    $key = Plugin::get_slug();

	    return empty( $cached_item_details[$key] );

    }

	/**
	 * Return true if we on plugin settings page.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return boolean
	 **/
	private function is_settings_page() {

	    return false !== strpos( $_SERVER[ 'REQUEST_URI' ], 'page=t42_content_protector_settings' );

    }

	/**
	 * Get item details from updates server.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return stdClass|false
	 **/
	private function get_remote_item() {

		/** Build URL to get Envato Item details by plugin slug. */
		$url = 'https://updates.42theme.com/wp-json/t42-purchase-validator/v1/get-plugin-details';
		$url .= '?plugin-slug=' . urlencode( Plugin::get_slug() );

		/** Get remote plugin details. */
		$plugin_details = Helper::get_instance()->get_remote( $url );

		/** Return FALSE on error. */
		if ( false === $plugin_details ) { return false; }

		/** Convert JSON to object. */
		$plugin_details = json_decode( $plugin_details, false );

		/** Return FALSE on error. */
		if ( null === $plugin_details || empty( $plugin_details ) ) { return false; }

		return $plugin_details;

	}

	/**
	 * Return CodeCanyon Item URL.
	 *
	 * @since 1.0.0
	 * @access public
     *
	 * @return string
	 **/
	public function get_url() {

		/** Try to get url from plugin details. */
		if ( is_array( self::$item ) && array_key_exists( 'codecanyon-url', self::$item ) ) {

		    return self::$item['codecanyon-url'];

        }

		return '';

	}

	/**
	 * Return Documentation URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 **/
	public function get_documentation_url() {

		/** Try to get url from plugin details. */
		if ( is_array( self::$item ) && array_key_exists( 'documentation-url', self::$item ) ) {

			return self::$item['documentation-url'];

		}

		return '';

	}

	/**
	 * Return Start Here widget content.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 **/
	public function get_widget_start_here() {

		/** Try to get url from plugin details. */
		if ( is_array( self::$item ) && array_key_exists( 'widget-start-here', self::$item ) ) {

			return self::$item['widget-start-here'];

		}

		return '';

	}

	/**
	 * Return CodeCanyon Item ID.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return int
	 **/
	public function get_id() {

		/** Prepare 'plugin_slug_envato_id' option name to store CodeCanyon Item ID. */
		$opt_name = Plugin::get_snake_slug() . '_envato_id';

		/** Do we have stored Envato ID? */
		$item_id = get_option( $opt_name );

		/** If we have stored Envato ID, return it. */
		if ( $item_id ) { return $item_id; }

		/** Else try to get id from plugin details. */
        if ( is_array( self::$item ) && array_key_exists( 'envato-item-id', self::$item ) ) {

            /** Store local option if this is real item ID. */
	        $item_id = (int) self::$item['envato-item-id'];
		    if ( $item_id > 0 ) { update_option( $opt_name, $item_id ); }

            return $item_id;

        }

		/** Return 0 in all other cases. */
        return 0;

	}

	/**
	 * Main EnvatoItem Instance.
	 *
	 * Insures that only one instance of EnvatoItem exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
     *
	 * @return EnvatoItem
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

} // End Class EnvatoItem.
