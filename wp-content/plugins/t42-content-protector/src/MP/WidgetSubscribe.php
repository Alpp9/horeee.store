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
 * WidgetSubscribe class used to implement sidebar widget Subscribe.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class WidgetSubscribe extends Widget {

	/**
	 * The one true WidgetSubscribe.
	 *
	 * @since 1.0.0
	 * @var WidgetSubscribe
	 **/
	private static $instance;

	/**
	 * Sets up a new WidgetSubscribe instance.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function __construct() {

		$this->widget_id ='subscribe';
		$this->title = esc_html__( 'Subscribe', 't42-content-protector' );

		/** AJAX subscribe. */
		add_action( 'wp_ajax_subscribe', [ $this, 'ajax_subscribe' ] );

	}

	/**
	 * Ajax subscribe to news letters.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return void
	 **/
	public function ajax_subscribe() {

		/** Check nonce for security. */
		check_ajax_referer( 't42-content-protector', 'nonce' );

		/** Prepare variables. */
		$name = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_STRING );
		$mail = filter_var( $_POST[ 'mail' ], FILTER_VALIDATE_EMAIL );
		$slug = Plugin::get_slug();

		/** Do we have valid email? */
		if ( ! $mail ) {
			wp_send_json( 'Wrong E-mail value.' );
		}

		/** Build request url. */
		$url = 'https://updates.42theme.com/wp-json/t42-purchase-validator/v1/subscribe?';
		$url .= 'mail=' . $mail;
		$url .= '&name=' . $name;
        $url .= '&slug=' . $slug;

		/** Send subscribe request. */
		$subscribe_result = Helper::get_instance()->get_remote( $url );

		/** Return FALSE on error. */
		if ( false === $subscribe_result ) { wp_send_json( 'Subscription failed.' ); }

		/** Return Subscribe result. */
		wp_send_json( $subscribe_result );

	}

	/**
	 * Render widget content.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	protected function render_content() {

		/** Get subscriber name and email. */
		list( $name, $mail ) = Helper::get_instance()->get_current_user_details();

		?>
		<div class="t42-card-body">
			<div class="t42-form-stacked t42-margin-large">
                <p>
                    <?php esc_html_e( 'Subscribe now and receive a box filled with hand-picked awesome items.', 't42-content-protector' ); ?>
                </p>

                <div class="t42-form-stacked">

                    <div class="t42-margin">
                        <div class="t42-inline t42-width-1-1">
                            <span class="t42-form-icon" t42-icon="icon: fi-user; ratio: 0.8"></span>
                            <input value="<?php esc_attr_e( $name ); ?>" class="t42-input t42-subscribe-name" type="text">
                        </div>
                    </div>

                    <div class="t42-margin">
                        <div class="t42-inline t42-width-1-1">
                            <span class="t42-form-icon" t42-icon="icon: fi-mail; ratio: 0.8"></span>
                            <input value="<?php esc_attr_e( $mail ); ?>" class="t42-input t42-subscribe-mail" placeholder="name@examle.com" type="email">
                        </div>
                        <div class="t42-margin-small-top t42-text-muted"><?php esc_html_e( 'We\'ll never share your email with anyone else.', 't42-content-protector' ); ?></div>
                    </div>

                    <div class="t42-margin-small-top">
                        <button class="t42-button t42-button-primary t42-waves-effect t42-waves-light t42-subscribe-btn">Subscribe</button>
                    </div>

                </div>
			</div>
		</div>
		<?php

	}

	/**
	 * Main WidgetSubscribe Instance.
	 * Insures that only one instance of WidgetSubscribe exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return WidgetSubscribe
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

} // End class WidgetSubscribe.
