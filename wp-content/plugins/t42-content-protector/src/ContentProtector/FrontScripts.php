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
 * Load JavaScripts for Frontend Area.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class FrontScripts extends Assets {

	/**
	 * The one true FrontScripts.
	 *
	 * @since 1.0.0
	 * @var FrontScripts
	 **/
	private static $instance;

	/**
	 * Sets up a new FrontScripts instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	private function __construct() {

		/** JavaScrips for frontend. */
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

	}

	/**
	 * Add JavaScrips for frontend area.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function enqueue_scripts() {

		/** Checks if plugin should work on this page. */
		if ( ! TabAssignments::get_instance()->display() ) { return; }

		/** JavaScript is not allowed in AMP. */
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) { return; }

		/** Disable content protector for Elementor editor in the backend. */
		if ( isset( $_GET['elementor-preview'] ) ) { return; }

		/** For development and debug. */
		$deps = [];
		if  ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {

			/** Enqueue hotkeys.js script separately. */
			$this->enqueue_script( 'hotkeys' );
			$deps[] = $this->get_handle( 'hotkeys' );

			/**
			 * Micromodal.js is a tiny, dependency-free javascript library for creating accessible modal dialogs.
			 *
			 * @see https://micromodal.vercel.app/
			 * @see https://github.com/Ghosh/micromodal
			 **/
			$this->enqueue_script( 'micromodal' );
			$deps[] = $this->get_handle( 'micromodal' );

		}

		/** Enqueue Content protector script. */
		$this->enqueue_script( 'protector', false, $deps );

		/** Shorthand for plugin settings. */
		$opt = Settings::get_instance()->options;

		/** Prepare values to pass to JS. */
		$to_js = [
			'disableSelectAll'     => 'on' === $opt['select_all'],
			'disableCopy'          => 'on' === $opt['copy'],
			'disableCut'           => 'on' === $opt['cut'],
			'disablePaste'         => 'on' === $opt['paste'],
			'disableSave'          => 'on' === $opt['save'],
			'disableViewSource'    => 'on' === $opt['view_source'],
			'disablePrintPage'     => 'on' === $opt['print_page'],
			'disableDeveloperTool' => 'on' === $opt['developer_tools'],
			'disableReaderMode'    => 'on' === $opt['safari_reader'],
			'disableRightClick'    => 'on' === $opt['right_click'],
			'disableTextSelection' => 'on' === $opt['text_selection'],
			'disableImageDragging' => 'on' === $opt['image_drag'],
			'copyrightDialog'      => 'on' === $opt['copyright_dialog'],
		];
		wp_localize_script( $this->get_handle( 'protector' ), 't42ContentProtectorOptions', $to_js );

	}

	/**
	 * Main FrontScripts Instance.
	 * Insures that only one instance of FrontScripts exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return FrontScripts
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

} // End class FrontScripts.
