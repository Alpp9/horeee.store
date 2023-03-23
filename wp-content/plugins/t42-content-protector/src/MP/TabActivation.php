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
 * SINGLETON: Class used to implement Activation tab on plugin settings page.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class TabActivation extends Tab {

    /**
     * Slug of current tab.
     *
     * @since 1.0.0
     * @const TAB_SLUG
     **/
    const TAB_SLUG = 'activation';

	/**
	 * The one true PluginActivation.
	 *
	 * @var TabActivation
	 **/
	private static $instance;

	/**
	 * Generate Assignments Tab.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function add_settings() {

		/** Not show if plugin don't have Envato ID. */
		if ( ! EnvatoItem::get_instance()->get_id() ) { return; }

		/** Assignments Tab. */
		$this->add_settings_base( self::TAB_SLUG );

	}

	/**
	 * Render form with all settings fields.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function do_settings() {

		/** No tab, nothing to do. */
		if ( ! $this->is_enabled( self::TAB_SLUG ) ) { return; }

		/** Render title. */
		$this->render_title( self::TAB_SLUG );

		/** Render fields. */
		$this->do_settings_base( self::TAB_SLUG );

	}

	/**
	 * Output nonce, action, and option_page fields for a settings page.
	 * Prints out all settings sections added to a particular settings page
	 *
	 * @param string $tab_slug - Slug of tub to check.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
	protected function do_settings_base( $tab_slug = null ) {

		/** Foolproof. */
		if ( null === $tab_slug ) { return; }

		$prefix = 't42_content_protector_' . $tab_slug;

		?>
        <div class="t42-card-body">
            <div class="t42-form-stacked t42-margin-large">
				<?php

                settings_fields( $prefix . 'OptionGroup' );
                do_settings_sections( $prefix . 'OptionGroup' );

				$this->render_form();
				$this->render_FAQ();
                ?>
            </div>
        </div>
		<?php

	}

	/**
	 * Render CodeCanyon Activation Form
	 */
	public function render_form() {

	    /** Prepare variables. */
		$key = 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id();
		$options = Settings::get_instance()->options;

		/**
		 * The Purchase Code entered by the user must be stored in the wp_options table as a string
		 * using the following syntax:
		 *    option_name: envato_purchase_code_<item_id>
         *    option_value: <purchase_code>
         **/
        if ( array_key_exists( $key, $options ) ) {
            update_option( $key, $options[$key] );
        }

        ?>
        <div class="t42-activation-form">
            <h3><?php esc_html_e( 'Plugin Activation', 't42-content-protector' ); ?></h3>
            <?php
            /** Render activation field. */
            UI::get_instance()->render_activate_input(
	            $options[$key],
                esc_html__( 'Purchase code', 't42-content-protector'),
                esc_html__( 'Enter your CodeCanyon purchase code. Allowed only one Purchase Code per website.', 't42-content-protector' ),
                [
                    'name'          => 't42_content_protector_activation_settings[' . $key . ']',
                    'id'            => 't42-content-protector-activation-settings-purchase-code',
                    'placeholder'   => 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'
                ]
            );
            ?>
        </div>
        <?php

    }

    /**
     * Render FAQ block.
     *
     * @access public
     **/
    public function render_FAQ() {
        ?>
        <div class="t42-activation-faq t42-width-3-4@m">

            <h3><?php esc_html_e( 'Activation FAQ\'S', 't42-content-protector' ); ?></h3>

            <ul t42-accordion="multiple: true">

                <li>
                    <a class="t42-accordion-title" href="#">
                        <?php esc_html_e( 'Where is my Purchase Code?', 't42-content-protector' ); ?>
                    </a>
                    <div class="t42-accordion-content">
                        <p>
                            <?php esc_html_e( 'The purchase code is a unique combination of characters that confirms that you bought the plugin. You can find your purchase code in ', 't42-content-protector' ); ?>
                            <a href="https://1.envato.market/downloads-cc" target="_blank"><?php esc_html_e( 'your account', 't42-content-protector' );?></a>
		                    <?php esc_html_e( ' on the CodeCanyon. Learn more about ', 't42-content-protector' ); ?>
                            <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><?php esc_html_e( 'How to find your purchase code', 't42-content-protector' );?></a>
		                    <?php esc_html_e( ' .', 't42-content-protector');?>
                        </p>
                    </div>
                </li>

                <li>
                    <a class="t42-accordion-title" href="#">
                        <?php esc_html_e( 'Can I use one Purchase Code on multiple sites?', 't42-content-protector' ); ?>
                    </a>
                    <div class="t42-accordion-content">
                        <p>
		                    <?php esc_html_e( 'No, this is prohibited by license terms. You can use the purchase code on only one website at a time. Learn more about ', 't42-content-protector' ); ?>
                            <a href="https://1.envato.market/license-cc" target="_blank"><?php esc_html_e( 'Envato License', 't42-content-protector' );?></a>
		                    <?php esc_html_e( ' terms. ', 't42-content-protector' ); ?>
                        </p>
                    </div>
                </li>

                <li>
                    <a class="t42-accordion-title" href="#">
                        <?php esc_html_e( 'What are the benefits of plugin activation?', 't42-content-protector' ); ?>
                    </a>
                    <div class="t42-accordion-content">
                        <p>
		                    <?php esc_html_e( 'Activation of the plugin allows you to use all the functionality of the plugin on your site. In addition, in some cases, activating the plugin allows you to access additional features and capabilities of the plugin. Also, using an authored version of the plugin, you can be sure that you will not violate the license.', 't42-content-protector' ); ?>
                        </p>
                    </div>
                </li>

                <li>
                    <a class="t42-accordion-title" href="#">
	                    <?php esc_html_e( 'What should I do if my Purchase Code does not work?', 't42-content-protector' ); ?>
                    </a>
                    <div class="t42-accordion-content">
                        <p>
		                    <?php esc_html_e( 'There are several reasons why the purchase code may not work on your site. Learn more why your ', 't42-content-protector' ); ?>
                            <a href="https://help.market.envato.com/hc/en-us/articles/204451834-My-Purchase-Code-is-Not-Working" target="_blank"><?php esc_html_e( 'Purchase Code is Not Working', 't42-content-protector' );?></a>
		                    <?php esc_html_e( ' .', 't42-content-protector');?>
                        </p>
                    </div>
                </li>

            </ul>

        </div>
        <?php
    }

	/**
	 * Main PluginActivation Instance.
	 * Insures that only one instance of PluginActivation exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
     * @return TabActivation
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

} // End class TabActivation.
