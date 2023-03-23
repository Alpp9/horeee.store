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
 * WidgetHelpCenter class used to implement sidebar widget Help Center.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
class WidgetHelpCenter extends Widget {

	/**
	 * The one true WidgetHelpCenter.
	 *
	 * @since 1.0.0
	 * @var WidgetHelpCenter
	 **/
	private static $instance;

	/**
	 * Sets up a new WidgetHelpCenter instance.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function __construct() {

	    $this->widget_id ='help-center';
	    $this->title = esc_html__( 'Help Center', 't42-content-protector' );

		/** AJAX Contact Support. */
		add_action( 'wp_ajax_contact_support', [ $this, 'ajax_contact_support' ] );

    }

	/**
	 * Ajax contact support.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function ajax_contact_support() {

		/** Check nonce for security. */
		check_ajax_referer( 't42-content-protector', 'nonce' );

		/** Prepare variables. */
		$name = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_STRING );
		$mail = filter_var( $_POST[ 'mail' ], FILTER_VALIDATE_EMAIL );
		$message = filter_input( INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS );

        $page_from = filter_input( INPUT_POST, 'page_from', FILTER_SANITIZE_STRING );
		$slug = Plugin::get_slug();

		/** Do we have valid email? */
		if ( ! $mail ) {
			wp_send_json( 'Wrong E-mail value.' );
		}

		/** Build request url. */
		$url = 'https://updates.42theme.com/wp-json/t42-purchase-validator/v1/contact-support';

		$data = [
            'name'         => $name,
            'mail'         => $mail,
            'message'      => $message,
            'page_from'    => $page_from,
            'slug'         => $slug,
        ];

		/** Send message via POST request. */
		$sending_result = Helper::get_instance()->post_remote( $url, $data );

		/** Return FALSE on error. */
		if ( false === $sending_result ) { wp_send_json( 'Failed to send message.' ); }

		/** Return Subscribe result. */
		wp_send_json( $sending_result );

	}

	/**
	 * Render widget content.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
	protected function render_content() {

		?>
		<div class="t42-card-body">
			<div class="t42-form-stacked t42-margin-large">

                    <ul class="t42-nav t42-nav-default t42-nav-parent-icon" data-t42-nav="">

                        <li>
                            <a href="<?php echo esc_url( EnvatoItem::get_instance()->get_documentation_url() ); ?>" target="_blank">
                                <span data-t42-icon="icon:fi-text; ratio: 0.9" class="t42-margin-small-right t42-icon"></span>
                                <?php esc_html_e( 'Knowledge Base', 't42-content-protector' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="#t42-contact-support-modal" class="t42-contact-support-modal-btn" data-t42-toggle="">
                                <span data-t42-icon="icon:fi-support; ratio: 0.9" class="t42-margin-small-right t42-icon"></span>
	                            <?php esc_html_e( 'Contact Support', 't42-content-protector' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="#t42-contact-support-modal" class="t42-feature-request-modal-btn" data-t42-toggle="">
                                <span data-t42-icon="icon:fi-plus; ratio: 0.9" class="t42-margin-small-right t42-icon"></span>
	                            <?php esc_html_e( 'Feature Request', 't42-content-protector' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="#t42-contact-support-modal" class="t42-bur-report-modal-btn" data-t42-toggle="">
                                <span data-t42-icon="icon:fi-bug; ratio: 0.9" class="t42-margin-small-right t42-icon"></span>
	                            <?php esc_html_e( 'Report a Bug', 't42-content-protector' ); ?>
                            </a>
                        </li>

                    </ul>

			</div>
		</div>
		<?php

        /** Render Contact Support form in modal. */
        $this->render_contact_support_modal();

	}

	/**
	 * Render Contact Support form in modal.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
	private function render_contact_support_modal() {

		/** Get current user name and email. */
		list( $name, $mail ) = Helper::get_instance()->get_current_user_details();

	    ?>
        <div id="t42-contact-support-modal" t42-modal="">
            <div class="t42-modal-dialog t42-margin-auto-vertical">

                <button class="t42-modal-close-outside" type="button" t42-close=""></button>

                <div class="t42-modal-header">
                    <h2 class="t42-modal-title"><?php esc_html_e( 'Contact Support', 't42-content-protector' ); ?></h2>
                </div>

                <div class="t42-modal-body" t42-overflow-auto="">
                    <div class="t42-form-stacked t42-grid-small" t42-grid="">

                        <div class="t42-width-1-2">
                            <div class="t42-margin">
                                <div class="t42-inline t42-width-expand">
                                    <span class="t42-form-icon" t42-icon="icon: fi-user; ratio: 0.8"></span>
                                    <input value="<?php esc_attr_e( $name ); ?>" placeholder="<?php esc_html_e( 'Enter Your Name', 't42-content-protector' ); ?>" class="t42-input t42-contact-name" type="text">
                                </div>
                            </div>
                        </div>

                        <div class="t42-width-1-2">
                            <div class="t42-margin">
                                <div class="t42-inline t42-width-expand">
                                    <span class="t42-form-icon" t42-icon="icon: fi-mail; ratio: 0.8"></span>
                                    <input value="<?php esc_attr_e( $mail ); ?>" class="t42-input t42-contact-mail" placeholder="name@examle.com" type="email">
                                </div>
                            </div>
                        </div>

                        <div class="t42-width-1-1">
                            <div class="t42-margin">
                                <textarea class="t42-textarea t42-contact-message" rows="5" placeholder="<?php esc_html_e( 'Enter Your Message', 't42-content-protector' ); ?>"></textarea>
                            </div>
                        </div>

                        <div class="t42-margin-small-top t42-text-muted"><?php esc_html_e( 'We\'ll never share your email with anyone else.', 't42-content-protector' ); ?></div>
                    </div>

                </div>

                <div class="t42-modal-footer t42-text-right">
                    <button class="t42-button t42-button-default t42-align-left t42-margin-remove t42-modal-close" type="button">
						<?php esc_html_e( 'Close', 't42' ); ?>
                    </button>
                    <button class="t42-button t42-button-primary t42-send-btn" type="button">
	                    <?php esc_html_e( 'Send Message', 't42' ); ?>
                    </button>
                </div>

            </div>
        </div>
        <?php


    }

	/**
	 * Main WidgetHelpCenter Instance.
	 * Insures that only one instance of WidgetHelpCenter exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return WidgetHelpCenter
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

} // End class WidgetHelpCenter.
