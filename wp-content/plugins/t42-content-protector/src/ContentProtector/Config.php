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
 * SINGLETON: Settings class used to modify default plugin settings.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class Config {

	/**
	 * The one true Config.
	 *
     * @since 1.0.0
     * @access private
	 * @var Config
	 **/
	private static $instance;

	/**
	 * Prepare list of critical initial checks to run.
	 * Available checks: 'php56', 'curl'
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 **/
	public function get_initial_checks() {

		return [
			'php56', // Plugin need PHP 5.6+.
			'curl' // Plugin need cURL extension.
 		];

	}

	/**
	 * Prepare plugin features configurations by modifying the defaults.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	private function prepare_config() {

		/** Get default plugin config. */
		$config = Plugin::get_config();

		/** Merlin WP. */
		$config['merlin']['enabled'] = true;

		/** Update config. */
		Plugin::set_config( $config );

	}

    /**
     * Prepare plugin settings by modifying the default one.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function prepare_settings() {

	    /** Prepare custom plugin features configurations. */
	    $this->prepare_config();

        /** Get default plugin settings. */
        $tabs = Plugin::get_tabs();

        /** Remove 'Delete plugin, settings and data' option from Uninstall tab. */
        unset( $tabs['uninstall']['fields']['delete_plugin']['options']['plugin+settings+data'] );

	    /** Remove unused 'Custom CSS' tab. */
	    /** @noinspection UnsetConstructsCanBeMergedInspection */
	    unset( $tabs['custom_css'] );

	    /** Create General tab. */
	    $tabs = $this->create_tab_general( $tabs );

        /** Set updated tabs. */
        Plugin::set_tabs( $tabs );

        /** Refresh settings. */
        Settings::get_instance()->get_options();

    }

	/**
	 * Create General tab.
	 *
	 * @param array $tabs - List of tabs with all settings and fields.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return array - List of tabs with all settings and fields.
	 **/
	private function create_tab_general( $tabs ) {

        /** Select All. */
        $tabs[ 'general' ][ 'fields' ][ 'select_all' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable Select All:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Protect Your Text from Being Copied by Select All HotKeys.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable HotKeys: Ctrl+A, ⌘+A.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

        /** Disable Copy. */
        $tabs[ 'general' ][ 'fields' ][ 'copy' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable Copy:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Protect Your Text from Being Copied by Copy HotKeys.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable HotKeys: Ctrl+C, ⌘+C.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

        /** Disable Cut. */
        $tabs[ 'general' ][ 'fields' ][ 'cut' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable Cut:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Protect Your Text from Being Copied by Cut HotKeys.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable HotKeys: Ctrl+X, ⌘+X.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

        /** Disable Paste. */
        $tabs[ 'general' ][ 'fields' ][ 'paste' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable Paste:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Disable Paste HotKeys.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable HotKeys: Ctrl+V, ⌘+V.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

        /** Disable Save. */
        $tabs[ 'general' ][ 'fields' ][ 'save' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable Save:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Protect Your Text from Being Saved by Save HotKeys.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable HotKeys: Ctrl+S, ⌘+S.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

        /** Disable View Source. */
        $tabs[ 'general' ][ 'fields' ][ 'view_source' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable View Source:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Disable to View Source Code of Page by HotKeys.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable HotKeys: Ctrl+U, ⌘+U.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

        /** Disable Print Page. */
        $tabs[ 'general' ][ 'fields' ][ 'print_page' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable Print Page:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Protect Your Page from Being Printed by HotKeys.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable HotKeys: Ctrl+P, ⌘+P.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

        /** Disable Developer Tools. */
        $tabs[ 'general' ][ 'fields' ][ 'developer_tools' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable Developer Tools:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Disable to View Source Code of Page by Developer Tools.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable HotKeys: Ctrl+Shift+I, ⌘+⌥+I.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

        /** Disable Safari Reader. */
        $tabs[ 'general' ][ 'fields' ][ 'safari_reader' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable Safari Reader:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Protect Your Text and Images from being copied in the Safari Reader mode.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable Reader mode in Safari: ⌘+Shift+R.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

        /** Disable Right Click. */
        $tabs[ 'general' ][ 'fields' ][ 'right_click' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable Right Click:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Protect Your Content from Being Copied by Context Menu.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable Mouse Right Click.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

        /** Disable Text Selection. */
        $tabs[ 'general' ][ 'fields' ][ 'text_selection' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable Text Selection:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Disable Text Selection.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable Text Highlight by Mouse.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

        /** Disable Image Dragging. */
        $tabs[ 'general' ][ 'fields' ][ 'image_drag' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'Disable Image Dragging:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'Disable Image Dragging.', 't42-content-protector' ),
            'description' => esc_html__( 'Disable Image Dragging by Mouse.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

		/** Divider. */
		$tabs[ 'general' ][ 'fields' ][ 'divider_copyright_dialog' ] = [ 'type' => 'divider' ];

		/** Enable Copyright Dialog. */
		$tabs[ 'general' ][ 'fields' ][ 'copyright_dialog' ] = [
			'type' => 'switcher',
			'label' => esc_html__( 'Copyright Dialog:', 't42-content-protector' ),
			'show_label' => true,
			'placeholder' => esc_html__( 'Show Copyright Dialog.', 't42-content-protector' ),
			'description' => esc_html__( 'Enable Copyright warning when trying to copy.', 't42-content-protector' ),
			'show_description' => true,
			'default' => 'off',
		];

		/** Copyright Warning Message. */
		$tabs[ 'general' ][ 'fields' ][ 'copyright_message' ] = [
			'type' => 'editor',
			'label' => esc_html__( 'Copyright Warning Message', 't42-content-protector' ),
			'show_label' => true,
			'description' => esc_html__( 'You can use special placeholders: {siteName}, {siteDomain}, {currentDate}. They will be replaced with their values accordingly.', 't42-content-protector' ),
			'show_description' => true,
			'default' => $this->get_copyright_message(),
			'attr' => [
				'textarea_rows' => '15',
			]
		];

        /** Divider. */
        $tabs[ 'general' ][ 'fields' ][ 'divider_js' ] = [ 'type' => 'divider' ];

        /** JavaScript Required. */
        $tabs[ 'general' ][ 'fields' ][ 'js_required' ] = [
            'type' => 'switcher',
            'label' => esc_html__( 'JavaScript Required:', 't42-content-protector' ),
            'show_label' => true,
            'placeholder' => esc_html__( 'JavaScript Required.', 't42-content-protector' ),
            'description' => esc_html__( 'Protect Content if JavaScript is Disabled.', 't42-content-protector' ),
            'show_description' => true,
            'default' => 'on',
        ];

		/** JavaScript Required Message. */
        $tabs[ 'general' ][ 'fields' ][ 'js_message' ] = [
            'type' => 'editor',
            'label' => esc_html__( 'JavaScript Required Message', 't42-content-protector' ),
            'show_label' => true,
            'description' => '',
            'show_description' => false,
            'default' => '<h3>' . esc_html__( 'Please Enable JavaScript in your Browser to Visit this Site.', 't42-content-protector' ) . '</h3>',
            'attr' => [
                'textarea_rows' => '3',
            ]
        ];

		return $tabs;

	}

	/**
	 * Return HTML content for Copyright Message Dialog.
	 *
	 * @static
	 * @since 1.0.8
	 * @access private
	 *
	 * @return string
	 **/
	private function get_copyright_message() {

		$html = '<h4>' . esc_html__( 'Copyright Notice.', 't42-content-protector' ) . '</h4>';
		$html .= '<p>' . esc_html__( 'All Rights Reserved.', 't42-content-protector' ) . '</p>';
		$html .= '<p>' . esc_html__( 'All material appearing on the {siteName} website ("content") is protected by copyright under U.S. Copyright laws and is the property of {siteName}. You may not copy, reproduce, distribute, publish, display, perform, modify, create derivative works, transmit, or in any way exploit any such content, nor may you distribute any part of this content over any network, including a local area network, sell or offer it for sale, or use such content to construct any kind of database.', 't42-content-protector' ) . '</p>';

		return $html;

	}

	/**
	 * Main Settings Instance.
	 * Insures that only one instance of Settings exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return Config
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

} // End class Config.
