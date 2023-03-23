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
 * SINGLETON: Class used for check plugin compatibility on early phase.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class CheckCompatibility {

    /**
     * Array of messages to show in admin area if some checks fails.
     *
     * @var array
     * @since 1.0.0
     **/
    public $admin_messages = [];

    /**
     * The one true CheckCompatibility.
     *
     * @var CheckCompatibility
     * @since 1.0.0
     **/
    private static $instance;

    /**
     * Do initial hosting environment check: PHP version and critical extensions.
     *
     * @param bool $show_message - Show or hide messages in admin area.
     *
     * @since 1.0.0
     * @access public
     *
     * @return bool - true if all checks passed, false otherwise.
     **/
    public function do_initial_checks( $show_message = true ) {

        /** List of critical initial checks to run. List of available checks: 'php56', 'curl'. */
	    $checks = Config::get_instance()->get_initial_checks();

        /** Flag to indicate failed checks. */
        $pass = true;

	    /** Plugin require PHP 5.6 or higher. */
	    if ( in_array( 'php56', $checks, true ) ) {

		    /** @noinspection NestedPositiveIfStatementsInspection */
		    if ( false === $this->check_php56_version( $show_message ) ) { $pass = false; }

	    }

	    /** Plugin require cURL extension. */
	    if ( in_array( 'curl', $checks, true ) ) {

		    /** @noinspection NestedPositiveIfStatementsInspection */
		    if ( false === $this->check_curl( $show_message ) ) { $pass = false; }

	    }

        /** Add handler to show admin messages. */
        $this->admin_notices( $show_message );

        return $pass;

    }

    /**
     * Add handler to show admin messages.
     *
     * @param $show_message
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    private function admin_notices( $show_message ) {

        /** If we need to show message in admin area. */
        if ( ! $show_message ) { return; }

        /** Show messages as WordPress admin messages. */
        add_action( 'admin_notices', [$this, 'show_admin_messages'] );

    }

    /**
     * Show messages in Admin area.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function show_admin_messages() {

        /** Show messages as WordPress admin messages. */
        foreach ( $this->admin_messages as $message ) {
            $this->render_classic_message( $message, 'error' );
        }

    }

    /**
     * Render message in classic WordPress style.
     *
     * @param string $message   - Message to show
     * @param string $type      - Type of message: info|error|warning
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    private function render_classic_message( $message, $type = 'warning' ) {

        /** Render message in old fashion style. */
        ?>
        <div class="settings-error notice notice-<?php esc_attr_e( $type ); ?>">
            <h4><?php esc_html_e( 'Content Protector', 't42-content-protector' ); ?></h4>
            <p><?php esc_html_e( $message ); ?></p>
        </div>
        <?php

    }

    /**
     * Check minimal required php version.
     *
     * @param bool $show_message - Show or hide messages in admin area.
     *
     * @since 1.0.0
     * @access private
     *
     * @return bool - true if php version is 5.6 or higher, false otherwise.
     **/
    private function check_php56_version( $show_message = true ) {

        /** Plugin require PHP 5.6 or higher. */
        $res = ! ( ! defined( 'PHP_VERSION_ID' ) || PHP_VERSION_ID < 50600 );

        /** If we need to show message in admin area. */
        if ( false === $res && $show_message ) {

            $this->admin_messages[] = esc_html__( 'The minimum PHP version required for Content Protector plugin is 5.6.0.', 't42-content-protector' );

        }

        return $res;

    }

    /**
     * Check whether the cURL extension is installed.
     *
     * @param bool $show_message - Show or hide messages in admin area.
     *
     * @since 1.0.0
     * @access private
     *
     * @return bool - true if curl extension is loaded, false otherwise.
     **/
    private function check_curl( $show_message = true ) {

        /** Whether the cURL extension is installed. */
        $curl = ReporterServer::get_instance()->get_curl_installed();
        $check = ! $curl['warning'];

        /** If we need to show message in admin area. */
        if ( false === $check && $show_message ) {
            $this->admin_messages[] = $curl['recommendation'];
        }

        return $check;

    }

	/**
	 * Add warning message about failed initial checks.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
    public function add_failed_notice() {

        /** Show message for correct users. */
	    if( ! current_user_can( 'activate_plugins' ) ) { return; }

	    add_action( 'admin_notices', [$this, 'render_initial_checks_failed_msg'] );

    }

	/**
	 * Render Add warning message about failed initial checks.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
    public function render_initial_checks_failed_msg() {

	    /** Render message in old fashion style. */
	    ?>
        <div class="settings-error notice notice-warning">
            <h3><?php esc_html_e( 'Content Protector', 't42-content-protector' ); ?></h3>
            <p>
                <?php esc_html_e( 'The minimum requirements for the plugin are not met. Please check the system requirements in ', 't42-content-protector' ); ?>
                <a href="<?php echo esc_url( EnvatoItem::get_instance()->get_documentation_url() ); ?>" target="_blank" rel="nofollow">
                    <?php esc_html_e( 'the Plugin Documentation', 't42-content-protector' ); ?>
                </a>
            </p>
        </div>
	    <?php

    }

    /**
     * Main CheckCompatibility Instance.
     *
     * Insures that only one instance of CheckCompatibility exists in memory at any one time.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return CheckCompatibility
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

} // End Class CheckCompatibility.
