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
 * SINGLETON: The class contains main plugin logic.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class Sense {

	/**
	 * The one true Sense.
	 *
	 * @var Sense
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Setup the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function setup() {

		/** Define hooks that runs on both the front-end as well as the dashboard. */
		$this->both_hooks();

		/** Define public hooks. */
		$this->public_hooks();

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

		/** Hooks which should work everywhere in backend and frontend. */

	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function public_hooks() {

		/** Work only on frontend area. */
		if ( is_admin() ) { return; }

		/** Allow Google Page Speed Insights freely access to content. */
		if ( $this->is_google_page_speed_insights() ) { return; }

		/** Load CSS for Frontend Area. */
		FrontStyles::get_instance();

		/** Load JavaScripts for Frontend Area. */
		FrontScripts::get_instance();

		/** JavaScript Required protection. */
		add_action( 'wp_footer', [ $this, 'javascript_required' ] );

		/** Copyright Dialog. */
		add_action( 'wp_footer', [ $this, 'copyright_dialog' ] );

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

		/** Admin hooks and filters here. */

	}

	/**
	 * Detect Google Page Speed Insights by User Agent.
	 *
	 * @since 1.0.7
	 * @access public
	 *
	 * @return boolean TRUE for Google Page Speed Insights, FALSE otherwise.
	 **/
	public function is_google_page_speed_insights() {

	    /** Can't detect without user agent. */
	    if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) { return false; }

		/** Get the user agent. */
		$agent = $_SERVER["HTTP_USER_AGENT"];

		/** Substrings to detect Google Page Speed Insights. */
		$fingerprints = [
            'Google Page Speed Insights',
            'Chrome-Lighthouse'
        ];

		/** Search for Google Page Speed Insights fingerprint. */
		foreach ( $fingerprints as $fingerprint ) {

			/** This is Google Page Speed Insights. */
			if ( strpos( $agent, $fingerprint ) !== false ) { return true; }

		}

		/** False for all other cases. */
	    return false;

    }

	/**
	 * Protect Content if JavaScript is Disabled.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function javascript_required() {

		/** JavaScript is not allowed in AMP. */
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) { return; }

		/** Short hand for plugin settings. */
		$options = Settings::get_instance()->options;

		/** Work if JavaScript Required. */
		if ( 'on' !== $options['js_required'] ) { return; }

		ob_start();
		?>
		<noscript>
			<div id='t42-content-protector-js-disabled'>
				<div><?php echo wp_kses_post( $options['js_message'] ); ?></div>
			</div>
			<style>
                #t42-content-protector-js-disabled {
                    position: fixed;
                    top: 0;
                    left: 0;
                    height: 100%;
                    width: 100%;
                    z-index: 999999;
                    text-align: center;
                    background-color: #FFFFFF;
                    color: #000000;
                    font-size: 40px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
			</style>
		</noscript>
		<?php
		echo ob_get_clean();

	}

	/**
	 * Render Copyright Dialog.
	 *
	 * @since 1.0.8
	 * @access public
	 *
	 * @return void
	 **/
	public function copyright_dialog() {

		/** Checks if plugin should work on this page. */
		if ( ! TabAssignments::get_instance()->display() ) { return; }

		/** JavaScript is not allowed in AMP. */
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) { return; }

		/** Disable content protector for Elementor editor in the backend. */
		if ( isset( $_GET['elementor-preview'] ) ) { return; }

		/** Short hand for plugin settings. */
		$options = Settings::get_instance()->options;

		/** Work only if Copyright Dialog enabled. */
		if ( 'on' !== $options['copyright_dialog'] ) { return; }

		/** Prepare Copyright Message. */
		$copyright_message = $this->apply_replacements();

        ?>
        <!-- Start Content Protector WordPress Plugin -->
        <div id="t42-content-protector-copyright-dialog" aria-hidden="true">
            <div tabindex="-1" data-micromodal-close="">
                <div role="dialog" aria-modal="true" aria-labelledby="t42-content-protector-copyright-dialog-title" >
                    <header>
                        <button aria-label="<?php esc_html_e( 'Close Modal', 't42-content-protector' ); ?>" data-micromodal-close=""></button>
                    </header>
                    <div id="t42-content-protector-copyright-dialog-content">
                        <?php echo wp_kses_post( $copyright_message ) ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Content Protector WordPress Plugin -->
        <?php

    }

	/**
	 * Replace placeholders to values in Copyright Message.
	 *
	 * @since 1.0.8
	 * @access private
	 *
	 * @return string
	 **/
    private function apply_replacements() {

	    /** Short hand for plugin settings. */
	    $options = Settings::get_instance()->options;

        /** Prepare values for Replacements. */
		$site_name = get_bloginfo( 'name' );
		$site_domain = parse_url( site_url(), PHP_URL_HOST );
		$current_date = current_time( get_option( 'date_format' ) );

		/** Do replacements. */
	    $copyright_message = str_replace( [
			'{siteName}',
			'{siteDomain}',
			'{currentDate}',
		], [
			$site_name,
			$site_domain,
			$current_date,
		], $options['copyright_message'] );

	    return wpautop( $copyright_message );

    }

	/**
	 * Main Sense Instance.
	 *
	 * Insures that only one instance of Sense exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Sense
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self();

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

} // End Class Sense.
