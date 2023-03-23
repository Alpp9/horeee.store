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
 * SINGLETON: Class used to implement plugin settings page.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class Settings {

	/**
	 * All Plugin settings.
	 *
	 * @var array
	 * @since 1.0.0
	 **/
	public $options = [];

	/**
	 * The one true Settings.
	 *
	 * @var Settings
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Settings instance.
	 *
	 * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	private function __construct() {

		/** Get plugin settings. */
		$this->get_options();

		/** Add plugin settings page. */
		$this->add_settings_page();

		/** Instantiate sidebar widgets. */
		WidgetStartHere::get_instance();
		WidgetHelpCenter::get_instance();
		WidgetSubscribe::get_instance();

		/** AJAX close widget. */
		add_action( 'wp_ajax_close_widget', [ $this, 'ajax_close_widget' ] );

		/** AJAX reset tab settings. */
		add_action( 'wp_ajax_reset_tab_settings', [ $this, 'reset_tab_settings' ] );

	}

	/**
	 * Ajax reset tab settings.
	 *
	 * @since 1.0.1
	 * @access public
	 *
	 * @return void
	 **/
	public function reset_tab_settings() {

		/** Check nonce for security. */
		check_ajax_referer( 't42-content-protector', 'nonce' );

		/** Get tab slug to remove settings from. */
		$tab = filter_input( INPUT_POST, 'tab', FILTER_SANITIZE_STRING );

		/** Do we have valid tab? */
		if ( ! Plugin::get_tabs( "$tab:enabled" ) ) {
			wp_send_json( 'Wrong Tab param.' );
		}

		/** Prepare option name where settings are stored. */
		$tab_opt = Plugin::get_snake_slug() . '_' . $tab . '_settings';

		/** Reset all settings on tab. */
		$reset_result = delete_option( $tab_opt );

		/** Return reset result. */
		wp_send_json( $reset_result );

    }

	/**
	 * Ajax close widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function ajax_close_widget() {

		/** Check nonce for security. */
		check_ajax_referer( 't42-content-protector', 'nonce' );

		/** Prepare variables. */
		$widget_id = filter_input( INPUT_POST, 'widget_id', FILTER_SANITIZE_STRING );

		/** Do we have valid widget id? */
		if ( ! in_array( $widget_id, [ 't42-widget-help-center-btn', 't42-widget-subscribe-btn', 't42-widget-start-here-btn' ] ) ) {
			wp_send_json( 'Wrong widget id value.' );
		}

		/** Remove noise from widget id. */
		$widget_id = substr( $widget_id, 0, - 1 * strlen( '-btn' ) );
		if ( strpos( $widget_id, 't42-widget-' ) === 0 ) {
			$widget_id = substr($widget_id, strlen('t42-widget-'));
		}

		/** Prepare 'plugin_slug_widget_id_{user_id}' option name to store widget status. */
		$widget_opt = Plugin::get_snake_slug() . '_widget_' . $widget_id . '_' . get_current_user_id();

		/** Widget successfully closed for current user. */
		$closed_result = update_option( $widget_opt, '1' );

		/** Return closed result. */
		wp_send_json( $closed_result );

    }

	/**
	 * Render left column with menu.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function render_left_bar() {
        ?>
        <aside id="t42-left-col" class="t42-light t42-visible@m t42-position-fixed t42-desktop">
            <?php

            /** Render left navigation. */
            $this->render_left_nav();

            /** Render bottom bar with Save Changes button. */
            $this->render_bottom_bar();

            ?>
        </aside>
        <?php
    }

	/**
	 * Render left navigation.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function render_left_nav() {

		/** Selected tab key. */
		$current = $this->get_current_tab();

		/** Tabs array. */
		$tabs = Plugin::get_tabs();

		/** If the plugin haven't Envato ID, remove activation tab. */
		if ( 0 === EnvatoItem::get_instance()->get_id() ) {
			unset( $tabs['activation'] );
		}

		?>
        <div class="left-nav-wrap">
            <ul class="t42-nav t42-nav-default t42-nav-parent-icon" data-t42-nav="">
                <li class="t42-nav-header"><?php esc_html_e( 'Settings', 't42-content-protector' ) ?></li>

                <?php foreach ( $tabs as $tab => $value ) {

                    /** Skip disabled tabs. */
                    if ( ! Tab::is_tab_enabled( $tab ) ) { continue; }

                    /** Prepare CSS classes. */
                    $classes = [];
                    $classes[] = "t42-menu-tab-$tab";

                    /** Activate current tab. */
                    if ( $tab === $current ) {
                        $classes[] = 't42-active';
                    }

                    /** Prepare link. */
                    $link = '?page=t42_content_protector_settings&tab=' . $tab;
                    ?>
                    <li class="<?php esc_attr_e( implode( ' ', $classes ) ); ?>">
                        <a href="<?php esc_attr_e( $link ); ?>">
                            <span data-t42-icon="icon:<?php esc_attr_e( $value['icon'] ); ?>; ratio: 0.9" class="t42-margin-small-right"></span>
	                        <?php esc_html_e( $value['label'] ); ?>
                        </a>
                    </li>
                <?php } ?>

                <?php $this->render_installation_wizard(); // Render installation wizard item. ?>

            </ul>

        </div>
		<?php

	}

	/**
	 * Render installation wizard item.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function render_installation_wizard() {

	    /** Exit if merlin disabled or already completed. */
	    if ( ! Plugin::get_config('merlin:enabled') || get_option( 't42-content-protector_merlin_completed' ) ) { return; }

	    ?>
	    <li class="t42-nav-divider"></li>
        <li>
            <a href="<?php esc_url( menu_page_url( 't42-content-protector-wizard' ) ); ?>">
                <span data-t42-icon="icon:wizard" class="t42-margin-small-right"></span>
                <?php esc_html_e( 'Installation Wizard', 't42-content-protector' ); ?>
            </a>
        </li>
        <?php

    }

	/**
	 * Return first tab or selected tab.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 **/
	private function get_current_tab() {

	    /** First tab is default. */
		$tab = key( Plugin::get_tabs() );

		/** Or get active tab from url. */
		if ( isset ( $_GET['tab'] ) ) {

			$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );

		} elseif ( isset( $_POST['_wp_http_referer'] ) ) {

			$url = filter_input( INPUT_POST, '_wp_http_referer', FILTER_SANITIZE_STRING );
			$parts = parse_url( $url );

			if ( ! empty( $parts[ 'query' ] ) ) {

				parse_str( $parts[ 'query' ], $query );

				if ( ! empty( $query['tab'] ) ) {
					$tab = $query['tab'];
				}

            }

        }

		/** Tabs array. */
		$tabs = Plugin::get_tabs();

		/** Get first tab, if wrong tab slug. */
		if ( ! array_key_exists( $tab, $tabs ) ) {
			reset( $tabs );
			$tab = key( $tabs );
		}

		return $tab;

	}

	/**
	 * Render bottom bar with Save Changes button.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function render_bottom_bar() {
	    ?>
        <div class="bar-bottom">
            <button class="t42-save-settings-btn t42-button t42-button-primary t42-waves-effect t42-waves-dark" disabled="disabled">
				<?php esc_html_e( 'Save Changes', 't42-content-protector' ); ?>
            </button>
        </div>
        <?php
    }

	/**
	 * Render activation status block.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
    private function render_activation_status() {

        /** Do not show activation message, if we hasn't item ID. */
	    if ( 0 === EnvatoItem::get_instance()->get_id() ) { return; }

	    /** Do not show activation message, if we already activated. */
	    if ( PluginActivation::get_instance()->is_activated() ) { return; }

	    ?>
        <div class="t42-activation-status">
            <span t42-icon="fi-alert"></span>
            <?php esc_html_e( 'Your copy of Content Protector plugin is not activated.', 't42-content-protector' ); ?>

	        <?php if ( 'activation' !== ( isset( $_GET['tab'] ) ? $_GET['tab'] : null ) ) : ?>
                <a href="<?php echo admin_url( 'admin.php?page=t42_content_protector_settings&tab=activation' ); ?>" class="t42-button t42-button-secondary t42-button-small t42-waves-effect t42-waves-dark t42-activate-btn"><?php esc_html_e( 'Activate Now', 't42-content-protector' ); ?></a>
            <?php endif; ?>
        </div>
	    <?php

    }

	/**
	 * Render logo block.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
    private function render_logo() {
	    ?>
        <div class="left-logo t42-flex t42-flex-middle">
            <a href="<?php echo admin_url( 'admin.php?page=t42_content_protector_settings' ); ?>" class="t42-h1 t42-link-reset">
                <img class="t42-plugin-logo" src="<?php echo esc_attr( Plugin::get_url() ) . 'images/logo-color.svg'; ?>" alt="<?php esc_html_e( Plugin::get_name() ); ?>" width="42" height="42">
                <?php esc_html_e( Plugin::get_name() ); ?>
                <sup><?php echo esc_html__( 'ver.', 't42-content-protector' ) . esc_html( Plugin::get_version() ); ?></sup>
            </a>

        </div>
        <?php
    }

	/**
	 * Render offcanvas menu for settings page.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
    private function render_offcanvas() {
        ?>
        <div id="t42-offcanvas-nav" data-t42-offcanvas="flip: true; overlay: true" class="t42-offcanvas">
            <div class="t42-offcanvas-bar t42-offcanvas-bar-animation t42-offcanvas-slide">
                <button class="t42-offcanvas-close t42-close t42-icon" type="button" data-t42-close=""><svg width="14" height="14" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><line fill="none" stroke="#000" stroke-width="1.1" x1="1" y1="1" x2="13" y2="13"></line><line fill="none" stroke="#000" stroke-width="1.1" x1="13" y1="1" x2="1" y2="13"></line></svg></button>
                <h3>Title</h3>
                <p>Lorem ipsum dolor sit amet, sed do...</p>
            </div>
        </div>
        <?php
    }

	/**
	 * Render changelog modal.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
    private function render_changelog_modal() {

	    /** Exit, if changelog disabled. */
	    if ( ! Plugin::get_config( 'navbar-right:changelog' ) ) { return; }

        ?>

        <div id="t42-changelog-modal" t42-modal="">
            <div class="t42-modal-dialog">

                <button class="t42-modal-close-outside" type="button" t42-close=""></button>

                <div class="t42-modal-header">
                    <h2 class="t42-modal-title"><?php esc_html_e( Plugin::get_name() ); ?></h2>
                </div>

                <div class="t42-modal-body" t42-overflow-auto="">
                    <?php Changelog::get_instance()->render_changelog(); // Render "Changelog". ?>
                </div>

                <div class="t42-modal-footer t42-text-right">
                    <button class="t42-button t42-button-secondary t42-waves-effect t42-waves-dark t42-modal-close" type="button">
                        <?php esc_html_e( 'Close', 't42' ); ?>
                    </button>
                </div>

            </div>
        </div>

        <?php

    }

	/**
	 * Render content part of settings page.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
    private function render_content() {

	    /** Tabs array. */
	    $tabs = Plugin::get_tabs();

	    /** Get current tab settings. */
        $tab_slug = $this->get_current_tab();
	    $tab = $tabs[$tab_slug];

	    /** No content for disabled tab. */
	    if ( ! $tab['enabled'] ) { return; }

	    /** No content for tab without handler. */
	    if ( empty( $tab['class'] ) ) { return; }

	    ?>
        <div id="t42-content" data-t42-height-viewport="expand: true">
            <div class="t42-container t42-container-expand <?php esc_attr_e( 't42-tab-' . $tab_slug . '-box' ); ?>">
                <div class="t42-grid t42-grid-medium" data-t42-grid="">
                    <div class="t42-width-3-4">
                        <div class="t42-card t42-card-default t42-card-small">
	                        <?php call_user_func( [ $tab[ 'class' ], 'get_instance' ] )->do_settings( $tab_slug ); // Call settings from class for current tab. ?>
                        </div>
                    </div>
                    <?php if ( Plugin::get_config( 'sidebar-right:enabled' ) ) : ?>
                    <div class="t42-width-1-4">
                        <?php $this->render_sidebar(); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php
                /** Render footer on settings page. */
                $this->render_footer();
                ?>
            </div>
        </div>
        <?php
    }

	/**
	 * Render right sidebar.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
    private function render_sidebar() {

        /** Render Start Here widget. */
        if ( Plugin::get_config( 'sidebar-right:start-here' ) ) {
	        WidgetStartHere::get_instance()->render();
        }

	    /** Render Help Center widget. */
	    if ( Plugin::get_config( 'sidebar-right:help-center' ) ) {
            WidgetHelpCenter::get_instance()->render();
	    }

	    /** Render Subscribe widget. */
	    if ( Plugin::get_config( 'sidebar-right:subscribe' ) ) {
		    WidgetSubscribe::get_instance()->render();
	    }

    }

	/**
	 * Render footer on Settings Page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
    private function render_footer() {

	    ob_start();
        ?>
        <footer class="t42-section t42-section-small t42-text-center">
            <hr>
            <p class="t42-text-small t42-text-center">
                <?php
                esc_html_e( 'Copyright © ', 't42-content-protector' );
                echo date( 'Y' );
                esc_html_e( ' | Created by', 't42-content-protector' );
                ?>
                <!--suppress HtmlUnknownTarget -->
                <a href="https://42theme.com/" rel="noreferrer" target="_blank">42Theme</a>
            </p>
        </footer>
        <?php
	    $footer_html = ob_get_clean();

	    /**
	     * Filters the footer on plugin settings page.
	     *
	     * @since 1.0.0
	     * @param string $footer_html - HTML for footer.
	     **/
	    $footer_html = apply_filters( 't42_content_protector_Settings_render_footer', $footer_html );

	    echo wp_kses_post( $footer_html );

    }

	/**
	 * Render header part for Settings Page.
	 *
	 * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	private function render_header() {

		?>
        <header id="t42-top-head" class="t42-position-fixed">
            <div class="t42-container t42-container-expand t42-background-default">
                <nav class="t42-navbar" data-t42-navbar="mode:click; duration: 250">
                    <div class="t42-navbar-left">
                        <?php
                        /** Render logo block. */
                        $this->render_logo();

                        /** Render activation status. */
                        $this->render_activation_status();
                        ?>
                    </div>
                    <div class="t42-navbar-right">
                        <ul class="t42-navbar-nav">

                            <?php if ( Plugin::get_config( 'navbar-right:documentation' ) ) : ?>
                                <li>
                                    <a href="<?php echo esc_url( EnvatoItem::get_instance()->get_documentation_url() ); ?>"
                                       data-t42-icon="icon:fi-text"
                                       title="<?php esc_html_e( 'Documentation', 't42-content-protector' ); ?>"
                                       data-t42-tooltip=""
                                       target="_blank"
                                       class="t42-waves-effect t42-waves-dark">
                                    </a>
                                </li>
                            <?php endif; ?>

		                    <?php if ( Plugin::get_config( 'navbar-right:changelog' ) ) : ?>
                                <li>
                                    <a href="#t42-changelog-modal"
                                       data-t42-icon="icon:fi-list"
                                       title="<?php esc_html_e( 'Changelog', 't42-content-protector' ); ?>"
                                       data-t42-tooltip=""
                                       data-t42-toggle=""
                                       class="t42-waves-effect t42-waves-dark">
                                    </a>
                                </li>
		                    <?php endif; ?>

		                    <?php if ( Plugin::get_config( 'navbar-right:news' ) ) : ?>
                                <li>
                                    <a href="#t42-offcanvas-nav" data-t42-icon="icon:rss" title="<?php esc_html_e( 'News', 't42-content-protector' ); ?>" data-t42-tooltip="" data-t42-toggle="" ></a>
                                </li>
		                    <?php endif; ?>

                        </ul>
                    </div>
                </nav>
            </div>
        </header>
		<?php
	}

	/**
	 * Render plugin settings page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_options_page() {

		/** Security check. */
		if ( ! current_user_can( 'manage_options' ) ) { return; }

		?>
        <!--suppress HtmlUnknownTarget -->
        <form class="t42-form t42-form-stacked" action='options.php' method='post'>
	        <?php

            /** Render header part for Settings Page. */
            $this->render_header();

            /** Render left column with menu. */
            $this->render_left_bar();

            /** Render content part of settings page. */
            $this->render_content();

	        /** Render offcanvas menu for settings page. */
	        $this->render_offcanvas();

	        /** Render changelog modal. */
            $this->render_changelog_modal();

		?>
        </form>
        <?php

    }

	/**
	 * Return settings array with default values.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 **/
	private function get_default_settings() {

		/** Get all plugin tabs with settings fields. */
		$tabs = Plugin::get_tabs();

		$default = [];

		/** Collect settings from each tab. */
		foreach ( $tabs as $tab ) {

			/** If current tab haven't fields. */
			if ( empty( $tab['fields'] ) ) { continue; }

			/** Collect default values from each field. */
			foreach ( $tab['fields'] as $field_slug => $field ) {

				/** Skip diver, header, button and button group fields. They don't have value. */
			    if ( in_array( $field['type'], ['divider', 'header', 'button', 'button_group'] ) ) { continue; }

				/** Skip fields without default value. */
			    if ( ! array_key_exists( 'default', $field ) ) { continue; }

				/** Single select default value can be set as array. Fix it. */
				if ( 'select' === $field['type'] && empty( $field['attr']['multiple'] ) && is_array( $field['default'] ) ) {
					$field['default'] = $field['default'][0];
				}

				$default[$field_slug] = $field['default'];

				/** Multiple values for colorpicker fields.*/
				if ( 'colorpicker' === $field['type'] ) {

                    $default[$field_slug . '_switcher'] = isset( $field['gradient_switcher'] ) ? $field['gradient_switcher'] : 'off';
					$default[$field_slug . '_gradient'] = isset( $field['gradient_default'] ) ? $field['gradient_default'] : 'linear-gradient( 135deg, #2AFADF 10%, #4C83FF 100%)';

                }

			}

		}

		return $default;

	}

	/**
	 * Get saved plugin settings or default values.
	 *
	 * @since 1.0.0
     * @access public
     *
	 * @return void
	 **/
	public function get_options() {

		/** Default values. */
		$defaults = $this->get_default_settings();

		$results = [];

		/** Get all plugin tabs with settings fields. */
		$tabs = Plugin::get_tabs();

		/** Collect settings from each tab. */
		foreach ( $tabs as $tab_slug => $tab ) {

			$opt_name = "t42_content_protector_{$tab_slug}_settings";
			$options = get_option( $opt_name );
			$results = wp_parse_args( $options, $defaults );
			$defaults = $results;

		}

		$this->options = $results;

	}

	/**
	 * Generate Settings Page.
	 *
	 * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function settings_init() {

		/** Tabs array. */
		$tabs = Plugin::get_tabs();

		/** Get current tab settings. */
		$tab_slug = $this->get_current_tab();
		$tab = $tabs[$tab_slug];

        /** Call add_settings from appropriate class for each tab. */
        call_user_func( [ $tab['class'], 'get_instance' ] )->add_settings( $tab_slug );

	}

	/**
	 * Add plugin settings page.
	 *
	 * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function add_settings_page() {

	    /** Add admin menu for plugin settings. */
		add_action( 'admin_menu', [$this, 'add_admin_menu'], 1000 );

		/** Generate Settings Page. */
		add_action( 'admin_init', [$this, 'settings_init'] );

		/** Add class 'folded' to body, to make wp admin menu collapsed. */
		add_filter( 'admin_body_class', [$this, 'folded_admin_menu'] );

	}

	/**
	 * Adds class 'folded' to body, to make wp admin menu collapsed.
	 *
	 * @since 1.0.0
	 * @access public
     *
     * @param  String $classes - Current body classes.
	 *
	 * @return string - Altered body classes.
	 **/
	public function folded_admin_menu( $classes ) {

		/** Get current screen object. */
		$screen = get_current_screen();
		if ( null === $screen ) { return $classes; }

		/** Work only on plugin settings page. */
		if ( ! in_array( $screen->base, Plugin::get_menu_bases(), true ) ) {
			return $classes;
        }

	    /** CSS class to make admin menu collapsed. */
		$folded_class = 'folded';

		/** Explode css classes string to array. */
		$classes = Helper::get_instance()->remove_multiple_spaces( $classes );
		$array = explode( ' ', strtolower( $classes ) );

		/** Add 'folded' class if not added yet. */
		if ( ! in_array( $folded_class, $array, true ) ) {
			$classes .= $folded_class;
        }

		return $classes;

    }

	/**
	 * Add admin menu for plugin settings.
	 *
	 * @since 1.0.0
	 * @access public
     *
	 * @return void
	 **/
	public function add_admin_menu() {

		add_menu_page(
			esc_html__( 'Content Protector Settings', 't42-content-protector' ),
			esc_html__( 'Content Protector', 't42-content-protector' ),
			'manage_options',
			't42_content_protector_settings',
			[$this, 'render_options_page'],
			$this->get_admin_menu_icon(),
			$this->get_admin_menu_position()
		);

		/** Add sub menu foreach settings tab. */
		$this->add_admin_submenu();

	}

	/**
	 * Add sub menu foreach settings tab.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	private function add_admin_submenu() {

		/** Dont work on plugin settings page. */
		if ( strpos( $_SERVER['REQUEST_URI'], 'page=t42_content_protector_settings' ) !== false ) { return; }

		/** Add sub menu foreach settings tab. */
		$tabs = Plugin::get_tabs();
		foreach ( $tabs as $tab_slug => $tab ) {

			/** Skip disabled tabs. */
			if ( ! $tab['enabled'] ) { continue; }

			/** Add sub page. */
			add_submenu_page(
				't42_content_protector_settings',
				$tab['title'],
				$tab['title'],
				'manage_options',
				'admin.php?page=t42_content_protector_settings&tab=' . $tab_slug
			);

		}

    }

	/**
	 * Calculate admin menu position based on plugin slug value.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 **/
	private function get_admin_menu_position() {

		$hash = md5( 't42-content-protector' );

		$int = (int) filter_var( $hash, FILTER_SANITIZE_NUMBER_INT );
		$int =  (int) ( $int / 1000000000000 );

		return '58.' . $int;

	}

	/**
	 * Return path to admin menu icon or base64 encoded image.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 **/
	private function get_admin_menu_icon() {

		return 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( Plugin::get_path() . 'images/logo-menu.svg' ) );

	}

	/**
	 * Main Settings Instance.
	 *
	 * Insures that only one instance of Settings exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
     *
	 * @return Settings
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

} // End Class Settings.

