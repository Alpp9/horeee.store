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
 * SINGLETON: Class used to implement changelog functionality.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class Changelog {

	/**
	 * The one true Changelog.
	 *
     * @since 1.0.0
	 * @var Changelog
	 **/
	private static $instance;

    /**
     * Sets up a new Changelog instance.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function __construct() {

        /** AJAX Check Updates. */
        add_action( 'wp_ajax_check_updates', [ __CLASS__, 'ajax_check_updates' ] );

    }

	/**
	 * Render "Changelog" field.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function render_changelog() {

        /** Do we have changelog in cache? */
	    $cache = new Cache();
        $key = 'changelog';
        $cached_changelog = $cache->get( $key );

        /** Show changelog from cache. */
        if ( ! empty( $cached_changelog ) ) {

            /** Print HTML changelog. */
            $cached_changelog = json_decode( $cached_changelog, true );
            $this->print_changelog( $cached_changelog[$key] );
            return;

        }

        /** Get changelog from remote host. */
        $remote_changelog = $this->get_changelog_remote();
        if ( false === $remote_changelog ) { return; }

        /** Store changelog in cache. */
        $cache->set( $key, [$key => $remote_changelog] );

		/** Print HTML changelog. */
        $this->print_changelog( $remote_changelog );

    }

    /**
     * Get changelog from remote host.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string|false
     **/
    public function get_changelog_remote() {

        /** Build changelog url. */
        $changelog_url = 'https://updates.42theme.com/changelogs/' . Plugin::get_slug() . '.html';

        /** Get fresh changelog file. */
        $changelog = wp_remote_get( $changelog_url );

        /** Check for errors. */
        if ( is_wp_error( $changelog ) || empty( $changelog['body'] ) ) { return false; }

        /** Now in $changelog we have changelog in HTML. */
        $changelog = $changelog['body'];

        /** This is not like our changelog. */
        if ( false === strpos( $changelog, '<h3>Changelog</h3>' ) ) { return false; }

        return $changelog;

    }

    /**
     * Print HTML changelog.
     *
     * @since 1.0.0
     * @param string $changelog - Full changelog in HTML.
     * @access public
     *
     * @return void
     **/
    private function print_changelog( $changelog ) {

        ?><div class="t42-changelog-box"><?php echo wp_kses( $changelog, Helper::get_kses_allowed_tags_changelog() ); ?></div><?php

    }

    /**
     * Ajax clear cache and check updates.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public static function ajax_check_updates() {

        /** Check nonce for security. */
        check_ajax_referer( 't42-content-protector', 'nonce' );

        /** Do we need to do a full reset? */
        if ( empty( $_POST['checkUpdates'] ) ) {  wp_send_json( 'Wrong Parameter Value.' ); }

        /** Clear cache table. */
        $cache = new Cache();
        $cache->drop_cache_table();

        /** Render changelog to variable. */
	    ob_start();
	    self::get_instance()->render_changelog();
	    $changelog = ob_get_clean();

        /** Return "Changelog" JSON result. */
        wp_send_json( $changelog );

    }

	/**
	 * Main Changelog Instance.
	 * Insures that only one instance of Changelog exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return Changelog
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

} // End class Changelog.
