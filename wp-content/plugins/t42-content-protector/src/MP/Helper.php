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

use stdClass;
use WP_Filesystem_Direct;

/**
 * SINGLETON: The class combines many small, independent and useful methods.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class Helper {

	/**
	 * The one true Helper.
	 *
	 * @var Helper
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Convert 'kebab-case' string to 'snake_case'.
	 *
	 * @param $kebab - Directory path to remove.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 **/
	public static function kebab_to_snake( $kebab ) {

		return str_replace( [ '-', ' ' ], '_', $kebab );

	}

	/**
	 * Remove directory with all contents.
	 *
	 * @param $dir - Directory path to remove.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function remove_directory( $dir ) {

		require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
		require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );

		$fileSystemDirect = new WP_Filesystem_Direct( false );
		$fileSystemDirect->rmdir( $dir, true );

	}

	/**
	 * Initializes WordPress filesystem.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return object WP_Filesystem
	 **/
	public static function init_filesystem() {

		$credentials = [];

		if ( ! defined( 'FS_METHOD' ) ) {

			define( 'FS_METHOD', 'direct' );

		}

		$method = defined( 'FS_METHOD' ) ? FS_METHOD : false;

		/** FTP */
		if ( 'ftpext' === $method ) {

			/** If defined, set credentials, else set to NULL. */
			$credentials['hostname'] = defined( 'FTP_HOST' ) ? preg_replace( '|\w+://|', '', FTP_HOST ) : null;
			$credentials['username'] = defined( 'FTP_USER' ) ? FTP_USER : null;
			$credentials['password'] = defined( 'FTP_PASS' ) ? FTP_PASS : null;

			/** FTP port. */
			if ( null !== $credentials['hostname'] && strpos( $credentials['hostname'], ':' ) ) {

				list( $credentials['hostname'], $credentials['port'] ) = explode( ':', $credentials['hostname'], 2 );

				if ( ! is_numeric( $credentials['port'] ) ) {

					unset( $credentials['port'] );

				}

			} else {

				unset( $credentials['port'] );

			}

			/** Connection type. */
			if ( defined( 'FTP_SSL' ) && FTP_SSL ) {

				$credentials['connection_type'] = 'ftps';

			} elseif ( ! array_filter( $credentials ) ) {

				$credentials['connection_type'] = null;

			} else {

				$credentials['connection_type'] = 'ftp';

			}

		}

		/** The WordPress filesystem. */
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {

			/** @noinspection PhpIncludeInspection */
			require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem( $credentials );

		}

		return $wp_filesystem;

	}

	/**
	 * Get remote contents.
	 *
	 * @param  string $url  The URL we're getting our data from.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return false|string The contents of the remote URL, or false if we can't get it.
	 **/
	public function get_remote( $url ) {

		$args = [
			'timeout'    => 15,
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36', // Chrome 83.0 Win10.
		];

		$response = wp_remote_get( $url, $args );

		if ( is_array( $response ) ) {

			return $response['body'];

		}

		/** Error while downloading remote file. */
		return false;

	}

	/**
	 * Get remote contents via POST.
	 *
	 * @param string $url The URL we're getting our data from.
	 * @param array $data Request arguments. Default empty array. Optional.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return false|string The contents of the remote URL, or false if we can't get it.
	 **/
	public function post_remote( $url, $data = [] ) {

		$args = [
			'timeout'       => 10,
			'blocking'      => true,
			'user-agent'    => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36', // Chrome 83.0 Win10.
			'body'          => $data,
		];

		$response = wp_remote_post( $url, $args );

		if ( is_array( $response ) ) {

			return $response['body'];

		}

		/** Error while downloading remote url. */
		return false;

	}

	/**
	 * Write content to the destination file.
	 *
	 * @param $destination - The destination path.
	 * @param $content - The content to write in file.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Returns true if the process was successful, false otherwise.
	 **/
	public function write_file( $destination, $content ) {

		/** Content for file is empty. */
		if ( ! $content ) { return false; }

		/** Build the path. */
		$path = wp_normalize_path( $destination );

		/** Define constants if undefined. */
		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );
		}

		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
		}

		/** Try to put the contents in the file. */
		global $wp_filesystem;

		$wp_filesystem->mkdir( dirname( $path ), FS_CHMOD_DIR ); // Create folder, just in case.

		$result = $wp_filesystem->put_contents( $path, $content, FS_CHMOD_FILE );

		/** We can't write file.  */
		if ( ! $result ) { return false; }

		return $result;

	}

	/**
	 * Send Action to our remote host.
	 *
	 * @param $action - Action to execute on remote host.
	 * @param $plugin - Plugin slug.
	 * @param $version - Plugin version.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function send_action( $action, $plugin, $version ) {

		$domain = parse_url( site_url(), PHP_URL_HOST );
		$admin = base64_encode( get_option( 'admin_email' ) );
		$pid = get_option( 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id() );

		$ch = curl_init();

		$url = 'https://updates.42theme.com/wp-json/t42-purchase-validator/v1/actions?';
		$url .= 'action=' . $action . '&';      // Action.
		$url .= 'plugin=' . $plugin . '&';      // Plugin Name.
		$url .= 'domain=' . $domain . '&';      // Domain Name.
		$url .= 'version=' . $version . '&';    // Plugin version.
		$url .= 'pid=' . $pid . '&';            // Purchase Code.
		$url .= 'admin_e=' . $admin;

		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		curl_exec( $ch );

	}

	/**
	 * Replication of file_get_contents that uses cURL instead.
	 *
	 * @param $url
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool|string
	 **/
	public function file_get_contents_curl( $url ) {

		$curl = curl_init();

		curl_setopt( $curl, CURLOPT_AUTOREFERER, TRUE );
		curl_setopt( $curl, CURLOPT_HEADER, 0 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_URL, $url);
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, TRUE );

		$data = curl_exec( $curl );

		/**
		 * Handle connection errors.
		 * Try to connect to mirror in Soviet Russia.
		 **/
		if ( curl_errno( $curl ) > 0 ) {

			curl_close( $curl );
			return false;

		}

		curl_close( $curl );

		return $data;

	}

	/**
	 * Remove multiple spaces, tabs, new lines.
	 *
	 * @param string $text - Text to remove spaces from.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string - Text without spaces.
	 **/
	public function remove_multiple_spaces( $text ) {

		$text = preg_replace( '/[\t\n\r\0\x0B]/', '', $text );
		$text = preg_replace( '/([\s])\1+/', ' ', $text );

		return trim( $text );

	}

	/**
	 * Allow SVG files in the media library.
	 *
	 * @param $mime_types - Current array of mime types.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array - Updated array of mime types.
	 **/
	public function allow_svg_uploads( $mime_types ) {

		/** Adding .svg extension. */
		$mime_types['svg']  = 'image/svg+xml';
		$mime_types['svgz'] = 'image/svg+xml';

		return $mime_types;

	}

	/**
	 * Render inline svg by id or icon name.
	 *
	 * @param int|string $icon - media id, or icon name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 **/
	public function get_inline_svg( $icon ) {

		/** If this users custom svg. */
		if ( is_numeric( $icon ) ) {
			$icon = get_attached_file( $icon );

			/** If icon from library. */
		} else {
			$icon = Plugin::get_path() . 'images/icons/' . $icon;
		}

		if ( ! is_file( $icon ) ) { return ''; }

		$svg_icon = file_get_contents( $icon );

		/** Escaping SVG with KSES. */
		$kses_defaults = wp_kses_allowed_html( 'post' );

		$svg_args = [
			'svg'   => [
				'class' => true,
				'aria-hidden' => true,
				'aria-labelledby' => true,
				'role' => true,
				'xmlns' => true,
				'width' => true,
				'height' => true,
				'viewbox' => true, // <= Must be lower case!
			],
			'g'     => [ 'fill' => true ],
			'title' => [ 'title' => true ],
			'path'  => [ 'd' => true, 'fill' => true, ],
		];

		$allowed_tags = array_merge( $kses_defaults, $svg_args );

		return wp_kses( $svg_icon, $allowed_tags );

	}

	/**
	 * Write data to log file.
	 *
	 * @param string|array|object $log - Data to log.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function write_log( $log )  {

		if ( is_array( $log ) || is_object( $log ) ) {

			/** @noinspection ForgottenDebugOutputInspection */
			error_log( print_r( $log, true ) );
			return;

		}

		/** @noinspection ForgottenDebugOutputInspection */
		error_log( $log );

	}

	/**
	 * Return allowed tags for changelog html.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 **/
	public static function get_kses_allowed_tags_changelog() {

		/** Allowed HTML tags in post. */
		$kses_defaults = wp_kses_allowed_html( 'post' );

		/** Allowed HTML tags and attributes in svg. */
		$svg_args = [
			'style' => [],
		];

		return array_merge( $kses_defaults, $svg_args );

	}

	/**
	 * Return current user name and email.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 **/
	public function get_current_user_details() {

		/** Get current user object. */
		$current_user = wp_get_current_user();
		if ( null === $current_user ) {
			$current_user             = new stdClass();
			$current_user->first_name = '';
			$current_user->user_login = '';
			$current_user->user_email = '';
		}

		/** Build user name. */
		$name = empty( $current_user->first_name ) ? $current_user->user_login : $current_user->first_name . ' ' . $current_user->last_name;
		$mail = $current_user->user_email;

		return [$name, $mail];

	}

	/**
	 * Main Helper Instance.
	 *
	 * Insures that only one instance of Helper exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Helper
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

} // End Class Helper.
