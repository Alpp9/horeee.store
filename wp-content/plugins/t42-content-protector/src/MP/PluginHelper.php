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

use DateTime;
use Exception;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement base plugin features.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class PluginHelper {

	/**
	 * The one true PluginHelper.
	 *
	 * @var PluginHelper
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new PluginHelper instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Add plugin links. */
		add_filter( 'plugin_action_links_' . Plugin::get_basename(), [$this, 'add_links'] );

		/** Add plugin meta. */
		add_filter( 'plugin_row_meta', [$this, 'add_row_meta'], 10, 2 );

		/** Load JS and CSS for Backend Area. */
		$this->enqueue_backend();

	}

	/**
	 * Load JS and CSS for Backend Area.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function enqueue_backend() {

		/** Add admin styles. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );

		/** Add admin javascript. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

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

		/** Get current screen or exit. */
		$screen = get_current_screen();
		if ( null === $screen ) { return; }

		/** Add styles only on WP Plugins page. */
		if ( 'plugins' !== $screen->base ) { return; }

		/** Enqueue a plugins.css. */
		AdminStyles::get_instance()->enqueue_style( 'plugins', true );

	}

	/**
	 * Add JS for admin area.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function admin_scripts() {

		/** Get current screen or exit. */
		$screen = get_current_screen();
		if ( null === $screen ) { return; }

		/** Add scripts only on WP Plugins page. */
		if ( 'plugins' !== $screen->base ) { return; }

		/** Enqueue a plugins.js. */
		AdminScripts::get_instance()->enqueue_script( 'plugins', true );

	}

	/**
	 * Add additional links on plugin page.
	 *
	 * @param array $links Current links: Deactivate | Edit

	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 **/
	public function add_links( $links ) {

		/** Add 'Documentation' link to beginning. */
		array_unshift( $links, '<a title="' . esc_html__( 'Documentation', 't42-content-protector' ) . '" href="' . esc_url( EnvatoItem::get_instance()->get_documentation_url() ) . '" target="_blank">' . esc_html__( 'Documentation', 't42-content-protector' ) . '</a>' );

		/** Add 'Settings' link to beginning. */
		array_unshift( $links, '<a title="' . esc_html__( 'Settings', 't42-content-protector' ) . '" href="' . admin_url( 'admin.php?page=t42_content_protector_settings' ) . '">' . esc_html__( 'Settings', 't42-content-protector' ) . '</a>' );

		/** Add 'Author' link to ending. */
		$links[] = '<a href="https://42theme.com/" target="_blank" class="t42-author-site"><img src="' . Plugin::get_url() . 'images/logo-author.svg" alt="' . esc_html__( '42Theme', 't42-content-protector' ) . '"> 42Theme</a>';

		return $links;

	}

	/**
	 * Add "Rate us" link on plugin page.
	 *
	 * @param array $links The array having default links for the plugin. Deactivate | Edit
	 * @param string $file The name of the plugin file.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 **/
	public function add_row_meta( $links, $file ) {

		if ( Plugin::get_basename() !== $file ) { return $links; }

		/** Get special class for special days. */
		$special_class = $this->get_special_class();

		$links[] = esc_html__( 'Rate us:', 't42-content-protector' ) . "
		<a href='https://codecanyon.net/downloads' 
		   target='_blank' 
		   rel='noopener' 
		   class='t42-rating-link'
		   title='" . esc_html__( 'Please take one minute to leave a review about your experience with us', 't42-content-protector' ) . "'>
			<span class='t42-rating-stars " . esc_attr__( $special_class ) . "'>
	            <span></span><span></span><span></span><span></span><span></span>
	        </span>
        </a>
        ";

		return $links;

	}

	/**
	 * Get special class for special days.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 **/
	private function get_special_class() {

		/** St. Valentine's Day. */
		if ( $this->is_date_between( '02-13', '02-15' ) ) {
			return 't42-st-valentines-day';
		}

		/** New Year. */
		if (
			$this->is_date_between( '12-30', '12-31' ) ||
			$this->is_date_between( '01-01', '01-02' )
		) {
			return 't42-new-year-day';
		}

		/** Women's Day. */
		if ( $this->is_date_between( '03-07', '03-07' ) ) {
			return 't42-womens-day';
		}

		/** Fool's Day. */
		if ( $this->is_date_between( '04-01', '04-01' ) ) {
			return 't42-fools-day';
		}

		/** Webmaster's day. */
		if ( $this->is_date_between( '04-04', '04-04' ) ) {
			return 't42-webmasters-day';
		}

		/** Astronautics day. */
		if ( $this->is_date_between( '04-12', '04-12' ) ) {
			return 't42-astronautics-day';
		}

		/** First day of Winter. */
		if ( $this->is_date_between( '12-1', '12-3' ) ) {
			return 't42-winter-first-day';
		}

		/** First day of Spring. */
		if ( $this->is_date_between( '03-1', '03-3' ) ) {
			return 't42-spring-first-day';
		}

		/** First day of Summer. */
		if ( $this->is_date_between( '06-1', '06-3' ) ) {
			return 't42-summer-first-day';
		}

		/** First day of Autumn. */
		if ( $this->is_date_between( '09-1', '09-3' ) ) {
			return 't42-autumn-first-day';
		}

		return '';

	}

	/**
	 * Return true on if current day in interval.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $start - Month and day numbers in format 'mm-dd' for interval start.
	 * @param string $end - Month and day numbers in format 'mm-dd' for interval end.
	 *
	 * @return boolean
	 **/
	private function is_date_between( $start, $end ) {

		try {

			/** Get current date. */
			$now = new DateTime();
			$now->setTime(0, 0);
			$year = date( 'Y' );

			$start_date = new DateTime( $year . '-' . $start );
			$end_date = new DateTime( $year . '-' . $end );

			return $start_date <= $now && $now <= $end_date;

		} catch ( Exception $e ) {

			return false;

		}

	}

	/**
	 * Main PluginHelper Instance.
	 *
	 * Insures that only one instance of PluginHelper exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return PluginHelper
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

} // End Class PluginHelper.

