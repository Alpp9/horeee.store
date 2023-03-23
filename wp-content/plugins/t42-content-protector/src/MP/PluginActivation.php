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

/**
 * SINGLETON: Class used to implement Activation tab on plugin settings page.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 */
final class PluginActivation {

	/**
	 * The one true PluginActivation.
	 *
	 * @var PluginActivation
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new PluginActivation instance.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function __construct() {

		/** Work only in admin area. */
		if ( ! is_admin() ) { return; }

	    $item_id = EnvatoItem::get_instance()->get_id();
		add_action( 'update_option_envato_purchase_code_' . $item_id, [$this, 'reset_temporary_activation'], 10 );

	}

	/**
	 * Reset temporary activation on every pid change.
	 *
	 * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function reset_temporary_activation() {

		delete_transient ( 'activated_' . EnvatoItem::get_instance()->get_id() );

		/** Reset updates cache. */
		PluginUpdater::get_instance()->reset_cache();

	}

	/**
	 * Get purchase code for activation.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param false $pid
	 *
	 * @return string|false
	 **/
	private function get_purchase_code( $pid = false ) {

		if ( $pid !== false ) { return $pid; }

		/** Get fresh PID from form. */
		$envato_pic = 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id();
		if ( isset( $_POST['t42_content_protector_activation_settings'][$envato_pic] ) ) {

			$purchase_code = filter_input( INPUT_POST, $envato_pic );

		}

		/** Or get PID from option. */
		else {

			$purchase_code = get_option( 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id() );

		}

		return $purchase_code;

	}

	/**
	 * Return Activation Status.
	 *
	 * @since 1.0.0
	 * @access public
     *
	 * @param bool|string $pid - Purchase Code. Optional.
     *
	 * @return boolean True if activated.
	 **/
	public function is_activated( $pid = false ) {

		/** Get purchase code for activation. */
		$purchase_code = $this->get_purchase_code( $pid );

		/** If we do not have $purchase_code then nothing to check. */
		if ( ! $purchase_code ) {  return false; }

		/** Clean and validate purchase code. */
		/** Remove spaces. */
		$purchase_code = trim( $purchase_code );

		/** Make sure the code is valid before sending it to Envato. */
		if ( ! preg_match( "/^(\w{8})-((\w{4})-){3}(\w{12})$/", $purchase_code ) ) { return false; }

	    /** Check temporary activation */
        $local_activation = $this->local_validation(); // 0 - if no local value, go to download.

		if ( $local_activation === 0 ) {

		    /** Need Remote validation. */
			$remote_activation = $this->remote_validation( $purchase_code );

			if ( $remote_activation ) {

                $this->temporary_activation( true );
				return true;

            }

			/** Not activated. */
			$this->temporary_activation( false );

			return false;

		}

		/** Use local activation. */
		$this->temporary_activation( $local_activation );

		return filter_var( $local_activation, FILTER_VALIDATE_BOOLEAN );

	}

	/**
	 * Set temporary activation.
	 *
	 * @param bool $activate - Temporary Activate/Deactivate.
	 *
	 * @since 1.0.0
	 * @access private
     *
     * @return void
	 **/
	private function temporary_activation( $activate ) {

		/** Timeout for temporary activation. */
		$hours = 48;

	    if ( filter_var( $activate, FILTER_VALIDATE_BOOLEAN ) ) {

		    set_transient( 'activated_' . EnvatoItem::get_instance()->get_id(), '1', $hours * HOUR_IN_SECONDS );

        } else {

		    set_transient( 'activated_' . EnvatoItem::get_instance()->get_id(), '0', $hours * HOUR_IN_SECONDS );

        }

    }

	/**
	 * Validate PID on local server.
	 *
	 * @return int|true|false - status of temporary activation status.
     *         0 - if we dont have local activation status and need remove validation.
     *
	 * @since 1.0.0
	 * @access private
     *
     * @return void
	 **/
	private function local_validation() {

		/** Get temporary activation status. */
		$tmp_activation_status = get_transient( 'activated_' . EnvatoItem::get_instance()->get_id() );

		/** Send query to server if we dont have temporary option. */
		if ( false === $tmp_activation_status ) {
			return 0;
        }

		return filter_var( $tmp_activation_status, FILTER_VALIDATE_BOOLEAN );

	}

	/**
	 * Validate PID on our server.
	 *
	 * @param $purchase_code - Envato Purchase Code.
	 *
	 * @since 1.0.0
	 * @access private
     *
	 * @return boolean
	 **/
	private function remote_validation( $purchase_code ) {

		$curl = curl_init();

		/** Prepare URL. */
		$url = 'https://updates.42theme.com/wp-json/t42-purchase-validator/v1/actions?';
		$url .= 'action=validate&';                         // Action.
		$url .= 'plugin=t42-content-protector&';             // Plugin slug.
		$url .= 'domain=' . $this->get_domain() . '&';      // Domain Name.
		$url .= 'version=' . Plugin::get_version() . '&';   // Plugin version.
		$url .= 'pid=' . $purchase_code . '&';              // Purchase Code.
		$url .= 'admin_e=' . base64_encode( get_option( 'admin_email' ) );

		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		$json = curl_exec( $curl );

		/**
		 * Handle connection errors.
		 * Show users an appropriate message asking to try again later.
		 **/
		if ( curl_errno( $curl ) > 0 ) {

			echo esc_html( 'Error connecting to: ' . $url . PHP_EOL . 'Please check your security plugins and add this url to white list.' );
			return false;

		}

		/**
		 * If we reach this point, we have a proper response.
		 * Get the response code to check if the content was found.
		 **/
		$responseCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

		/**
		 * Anything other than HTTP 200 indicates a request error.
		 * In this case, we again ask the user to try again later.
		 **/
		if ( $responseCode !== 200 ) {

			echo esc_html('Failed to get content due to an error: HTTP ' . $responseCode . PHP_EOL . 'URL: ' . $url );
			return false;
		}

		curl_close( $curl );

		$res = json_decode( $json, true );

		return true === $res;

	}

	/**
     * Return current domain. Parsed from site url.
     *
     * @since 1.0.0
     * @access private
     *
	 * @return string
	 **/
	private function get_domain() {

	    return parse_url( site_url(), PHP_URL_HOST );

    }

	/**
	 * Main PluginActivation Instance.
	 *
	 * Insures that only one instance of PluginActivation exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
     *
	 * @return PluginActivation|false
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

} // End Class PluginActivation.
