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
 * Plugin class used to prepare plugin variables.
 * It can be called in any time and work without any dependencies.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class Plugin {

    /**
	 * Plugin version.
	 *
     * @since 1.0.0
     * @static
     * @access private
	 * @var string
	 **/
	private static $version;

	/**
	 * Using minified css and js files if SCRIPT_DEBUG is turned off.
     *
     * @since 1.0.0
	 * @static
     * @access private
	 *
     * @var string
	 **/
	private static $suffix;

	/**
	 * URL to plugin folder, with trailing slash.
	 *
     * @since 1.0.0
	 * @static
     * @access private
	 *
     * @var string
	 **/
    private static $url;

	/**
	 * Full PATH to plugin folder, with trailing slash.
	 *
     * @since 1.0.0
	 * @static
     * @access private
	 *
     * @var string
	 **/
	private static $path;

	/**
	 * Plugin base name.
	 *
     * @since 1.0.0
	 * @static
     * @access private
	 *
     * @var string
	 **/
	private static $basename;

	/**
	 * Plugin admin menu bases.
	 *
     * @since 1.0.0
	 * @static
     * @access private
	 *
     * @var array
	 **/
	private static $menu_bases = [];

	/**
	 * Full path to main plugin file.
	 *
     * @since 1.0.0
	 * @static
     * @access private
	 *
	 * @var string
	 **/
    private static $plugin_file;

	/**
	 * Plugin slug base.
	 *
     * @since 1.0.0
	 * @static
     * @access private
	 *
	 * @var string
	 **/
    private static $slug;

    /**
     * Settings Tabs.
     *
     * Holds the list of all tabs and fields with settings.
     *
     * @since 1.0.0
     * @static
     * @access private
     *
     * @var array
     **/
    private static $tabs;

	/**
	 * Plugin features configurations.
	 *
	 * @since 1.0.0
	 * @static
	 * @access private
	 *
	 * @var array
	 **/
	private static $config;

    /**
     * The one true Plugin.
     *
     * @since 1.0.0
     * @access private
     * @var Plugin
     **/
    private static $instance;

    /**
     * Sets up a new Plugin instance.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function __construct() {

        /** Initialize main variables. */
	    $this->initialization();

    }

    /**
     * Initialize main variables.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function initialization() {

        /** Full path to main plugin file. */
        self::$plugin_file = dirname( dirname( __DIR__ ) ) . '/t42-content-protector.php';

		/** Set Plugin version. */
        self::$version = self::get_plugin_data( 'Version' );

		/** Plugin slug. */
		self::$slug = self::get_plugin_data( 'TextDomain' );

		/** Gets the plugin URL (with trailing slash). */
		self::$url = plugin_dir_url( self::$plugin_file );

		/** Gets the plugin PATH. */
		self::$path = plugin_dir_path( self::$plugin_file );

		/** Using minified css and js files if SCRIPT_DEBUG is turned off. */
		self::$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		/** Set plugin basename. */
		self::$basename = plugin_basename( self::$plugin_file  );

		/** Plugin settings page menu base. There may be several. */
		self::$menu_bases[] = 'toplevel_page_t42_content_protector_settings';

		/** Fill $tabs field with default settings. */
		$this->default_tabs_settings();

	    /** Fill $config field with default settings. */
	    $this->default_config_settings();
		
    }

	/**
	 * Fill $config field with default settings.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
    private function default_config_settings() {

    	/** Navbar right. */
	    self::$config['navbar-right'] = [
		    'documentation' => true, // Documentation icon in header.
		    'changelog'     => true, // Changelog icon in header.
            'news'          => false, // News icon in header.
	    ];

	    /** Sidebar right. */
	    self::$config['sidebar-right'] = [
		    'enabled'       => true, // Enable/disable right sidebar.
		    'start-here'    => true, // Start Here Widget
		    'help-center'   => true, // Help Center Widget
		    'subscribe'     => true, // Subscribe Widget
	    ];

	    /** Merlin Wizard. */
	    self::$config['merlin'] = ['enabled' => true];

    }

    /**
     * Fill $tabs field with default settings.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function default_tabs_settings() {

        /** Add General Tab. */
        $this->add_general_tab();

        /** Add Custom CSS Tab. */
        $this->add_custom_css_tab();

        /** Add Assignments Tab. */
        $this->add_assignments_tab();

        /** Add Activation Tab. */
        $this->add_activation_tab();

        /** Add Status Tab. */
        $this->add_status_tab();

        /** Add Updates Tab. */
        $this->add_updates_tab();

        /** Add Uninstall Tab. */
        $this->add_uninstall_tab();

    }

    /**
     * Add General Tab to settings page.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function add_general_tab() {

        /** Create empty General Tab. */
        self::$tabs['general'] = [
            'enabled'        => true,
            'class'          => TabGeneral::class,
            'label'          => esc_html__( 'General', 't42-content-protector' ),
            'title'          => esc_html__( 'General Settings', 't42-content-protector' ),
            'show_title'     => true,
	        'reset_settings' => false,
            'icon'           => 'fi-home',
            'fields'         => []
        ];

    }

    /**
     * Add Custom Css Tab to settings page.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function add_custom_css_tab() {

        self::$tabs['custom_css'] = [
            'enabled'           => true,
            'class'             => TabGeneral::class,
            'label'             => esc_html__( 'Custom CSS', 't42-content-protector' ),
            'title'             => esc_html__( 'Custom CSS', 't42-content-protector' ),
            'show_title'        => true,
            'reset_settings'    => false,
            'icon'              => 'fi-code',
            'fields'            => [
                'custom_css' => [
                    'type'              => 'css_editor',
                    'label'             => esc_html__( 'CSS', 't42-content-protector' ),
                    'show_label'        => true,
                    'description'       => esc_html__( 'Add custom CSS here.', 't42-content-protector' ),
                    'show_description'  => true,
                    'default'           => '',
                ]
            ]
        ];

    }

    /**
     * Add Assignments Tab to settings page.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function add_assignments_tab() {

        self::$tabs['assignments'] = [
            'enabled'           => true,
            'class'             => TabAssignments::class,
            'label'             => esc_html__( 'Assignments', 't42-content-protector' ),
            'title'             => esc_html__( 'Assignments Settings', 't42-content-protector' ),
            'show_title'        => true,
            'reset_settings'    => false,
            'icon'              => 'fi-assignments',
            'assignments'       => [
                'matching_method'   => true,
                'wordpress_content' => true,
                'home_page'         => true,
                'menu_items'        => true,
                'date_time'         => true,
                'user_roles'        => true,
                'url'               => true,
                'devices'           => true,
                'custom_php'        => true,
            ],
            'fields'            => [
                'assignments' => [
                    'type'              => 'hidden',
                    'default'           => '{|matchingMethod|:1,|WPContent|:0,|WPContentVal|:||,|homePage|:0,|menuItems|:0,|menuItemsVal|:||,|dateTime|:0,|dateTimeStart|:||,|dateTimeEnd|:||,|languages|:0,|languagesVal|:||,|userRoles|:0,|userRolesVal|:||,|URL|:0,|URLVal|:||,|devices|:0,|devicesVal|:||,|os|:0,|osVal|:||,|browsers|:0,|browsersVal|:||,|mobileBrowsersVal|:||,|IPs|:0,|IPsVal|:||,|PHP|:0,|PHPVal|:||}',
                ]
            ]
        ];

    }

    /**
     * Add Activation Tab to settings page.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function add_activation_tab() {

        /** Not show if plugin don't have Envato ID. */
        if ( ! EnvatoItem::get_instance()->get_id() ) { return; }

        self::$tabs['activation'] = [
            'enabled'           => true,
            'class'             => TabActivation::class,
            'label'             => esc_html__( 'Activation', 't42-content-protector' ),
            'title'             => esc_html__( 'Plugin Activation', 't42-content-protector' ),
            'show_title'        => false,
            'reset_settings'    => false,
            'icon'              => 'fi-key',
            'fields'            => [
                'envato_purchase_code_' . EnvatoItem::get_instance()->get_id() => [
                    'type'              => 'text',
                    'default'           => '',
                ]
            ]
        ];

    }

    /**
     * Add Status Tab to settings page.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function add_status_tab() {

        self::$tabs['status'] = [
            'enabled'           => true,
            'class'             => TabStatus::class,
            'label'             => esc_html__( 'Status', 't42-content-protector' ),
            'title'             => esc_html__( 'System Status', 't42-content-protector' ),
            'show_title'        => true,
            'reset_settings'    => false,
            'icon'              => 'fi-status',
            'reports'           => [
                'server'    => [
                    'enabled'               => true,
                    'os'                    => true,
                    'software'              => true,
                    'mysql_version'         => true,
                    'php_version'           => true,
                    'write_permissions'     => true,
                    'zip_installed'         => true,
                    'curl_installed'        => true,
                    'allow_url_fopen'       => false,
                    'elementor_installed'   => false,
                    'dom_installed'         => false,
                    'xml_installed'         => false,
                    'bcmath_installed'      => false,
	                'mbstring_installed'    => false,
	                'server_time'           => false,
                ],
                'wordpress'     => [
                    'enabled' => true
                ],
                'plugins'       => [
                    'enabled' => true
                ],
                'theme'         => [
                    'enabled' => true
                ],
            ]
        ];

    }

    /**
     * Add Updates Tab to settings page.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function add_updates_tab() {

        self::$tabs['updates'] = [
            'enabled'           => true,
            'class'             => TabGeneral::class,
            'label'             => esc_html__( 'Updates', 't42-content-protector' ),
            'title'             => esc_html__( 'Updates', 't42-content-protector' ),
            'show_title'        => true,
            'reset_settings'    => false,
            'icon'              => 'fi-updates',
            'fields'            => [
	            'check_updates' => [
		            'type'              => 'button',
		            'label'             => esc_html__( 'Check for updates', 't42-content-protector' ),
		            'show_label'        => false,
		            'button_text'       => esc_html__( 'Check Updates', 't42-content-protector' ),
		            'description'       => '',
		            'show_description'  => false,
		            'icon'              => 'refresh',
		            'attr'              => [
			            'class'     => 't42-button-primary',
		            ]
	            ],
	            'changelog'     => [
				    'type'              => 'changelog',
				    'render'            => [ Changelog::get_instance(), 'render_changelog' ],
				    'label'             => esc_html__( 'Changelog', 't42-content-protector' ),
				    'show_label'        => false,
				    'description'       => '',
				    'show_description'  => false
			    ]
            ]
        ];

    }

    /**
     * Add Uninstall Tab to settings page.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function add_uninstall_tab() {

        self::$tabs['uninstall'] = [
            'enabled'           => true,
            'class'             => TabGeneral::class,
            'label'             => esc_html__( 'Uninstall', 't42-content-protector' ),
            'title'             => esc_html__( 'Uninstall Settings', 't42-content-protector' ),
            'show_title'        => true,
            'reset_settings'    => false,
            'icon'              => 'fi-uninstall',
            'fields'            => [
                'delete_plugin' => [
                    'type'              => 'select',
                    'label'             => esc_html__( 'Delete mode', 't42-content-protector' ),
                    'show_label'        => true,
                    'description'       => esc_html__( 'Choose which data to delete on uninstall action.', 't42-content-protector' ),
                    'show_description'  => true,
                    'options'           => [
                        'plugin'                => esc_html__( 'Delete Plugin only', 't42-content-protector' ),
                        'plugin+settings'       => esc_html__( 'Delete Plugin and Settings', 't42-content-protector' ),
                        'plugin+settings+data'  => esc_html__( 'Delete Plugin, Settings and Data', 't42-content-protector' ),
                    ],
                    'default'           => ['plugin'],
                ]
            ]
        ];

    }

    /**
     * Return current plugin metadata.
     *
     * @param string $field - Field name from plugin data.
     *
     * @since 1.0.0
     * @access private
     *
     * @return string|array {
     *     Plugin data. Values will be empty if not supplied by the plugin.
     *
     *     @type string $Name        Name of the plugin. Should be unique.
     *     @type string $Title       Title of the plugin and link to the plugin's site (if set).
     *     @type string $Description Plugin description.
     *     @type string $Author      Author's name.
     *     @type string $AuthorURI   Author's website address (if set).
     *     @type string $Version     Plugin version.
     *     @type string $TextDomain  Plugin textdomain.
     *     @type string $DomainPath  Plugins relative directory path to .mo files.
     *     @type bool   $Network     Whether the plugin can only be activated network-wide.
     *     @type string $RequiresWP  Minimum required version of WordPress.
     *     @type string $RequiresPHP Minimum required version of PHP.
     * }
     **/
    private static function get_plugin_data( $field ) {

        static $plugin_data;

        /** We already have $plugin_data. */
        if ( $plugin_data !== null ) {
            return $plugin_data[$field];
        }

        /** This is first time call of method, so prepare $plugin_data. */
        if ( ! function_exists('get_plugin_data') ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        $plugin_data = get_plugin_data( self::get_plugin_file() );

        return $plugin_data[$field];

    }

    /**
     * Get Plugin version.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
    public static function get_version() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) { self::get_instance(); }

        return self::$version;

    }

    /**
     * Get Plugin Name.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
    public static function get_name() {

        return self::get_plugin_data( 'Name' );

    }

    /**
     * Get .min suffix.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
    public static function get_suffix() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) { self::get_instance(); }

        return self::$suffix;

    }

    /**
     * Get URL to plugin folder with trailing slash.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
    public static function get_url() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) { self::get_instance(); }

        return self::$url;

    }

    /**
     * Get full Path to plugin folder with trailing slash.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
    public static function get_path() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) { self::get_instance(); }

        return self::$path;

    }

    /**
     * Get Plugin Basename.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
    public static function get_basename() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) { self::get_instance(); }

        return self::$basename;

    }

    /**
     * Get plugin menu bases.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return array
     **/
    public static function get_menu_bases() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) { self::get_instance(); }

        return self::$menu_bases;

    }

    /**
     * Get path to plugin file.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
    public static function get_plugin_file() {

        return  dirname( dirname( __DIR__ ) ) . '/t42-content-protector.php';

    }

    /**
     * Get Plugin slug.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
    public static function get_slug() {

        return self::get_plugin_data( 'TextDomain' );

    }

	/**
	 * Get Plugin slug in snake case.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 **/
	public static function get_snake_slug() {

		return Helper::kebab_to_snake( self::get_plugin_data( 'TextDomain' ) );

	}

    /**
     * Get Plugin tabs or tabs value by key.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return array|string|null
     **/
    public static function get_tabs( $key = null ) {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) { self::get_instance(); }

	    /** Return full tabs if key is empty. */
	    if ( null === $key ) {
		    return self::$tabs;
	    }

	    /** Return element or null if not found. */
	    return self::search_by_key( $key, self::$tabs );

    }

	/**
	 * Get Plugin config value or full array.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string|null $key Access key in format: 'name:sub-name:element'
	 *
	 * @return array|string|null
	 **/
	public static function get_config( $key = null ) {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) { self::get_instance(); }

		/** Return full config if key is empty. */
		if ( null === $key ) {
			return self::$config;
		}

		/** Return element or null if not found. */
		return self::search_by_key( $key, self::$config );

	}

	/**
	 * Return element or null if not found.
	 *
	 * @param string $key Access key in format: 'name:sub-name:element'
	 * @param array $arr Array with settings to search
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array|string|null
	 **/
	private static function search_by_key( $key, $arr ) {

		/** Convert sting to array. */
		$keys = explode( ':', $key );

		/** Search element by $keys array. */
		$temp =& $arr;
		foreach ( $keys as $i ) {
			$temp =& $temp[$i];
		}

		/** Return element or null if not found. */
		return $temp;

	}

    /**
     * Set Plugin tabs.
     *
     * @param array $tabs - Tabs and fields with settings.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public static function set_tabs( $tabs ) {

        self::$tabs = $tabs;

    }

	/**
	 * Set Plugin features configuration.
	 *
	 * @param array $config - Features configuration.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public static function set_config( $config ) {

		self::$config = $config;

	}

    /**
     * Get instance of Plugin.
     *
     * Insures that only one instance of Plugin exists in memory at any one time.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return Plugin
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

} // End class Plugin.
