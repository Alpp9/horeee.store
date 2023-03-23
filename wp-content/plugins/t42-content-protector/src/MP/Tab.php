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
 * Base methods for Tabs Classes.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
abstract class Tab {

    /**
     * Check if tab exist and enabled.
     *
     * @param string $tab_slug - Slug of tub to check.
     *
     * @since 1.0.0
     * @access protected
     *
     * @return bool - True if Tab is enabled, false otherwise.
     **/
    protected function is_enabled( $tab_slug = null ) {

        /** Foolproof. */
        if ( null === $tab_slug ) { return false; }

        /** Get all tabs and settings. */
        $tabs = Plugin::get_tabs();

        /** Check if tab exist. */
        if ( ! isset( $tabs[ $tab_slug ] ) ) { return false; }

        /** Check if 'enabled' field of tab exist. */
        if ( ! isset( $tabs[ $tab_slug ][ 'enabled' ] ) ) { return false; }

        /** Check if tab is enabled. */
        return true === $tabs[ $tab_slug ][ 'enabled' ];

    }

    /**
     * Render tab title.
     *
     * @param string $tab_slug - Slug of tub to check.
     *
     * @since 1.0.0
     * @access protected
     *
     * @return void
     **/
    protected function render_title( $tab_slug = null ) {

        /** Foolproof. */
        if ( null === $tab_slug ) { return; }

        /** Get all tabs and settings. */
        $tabs = Plugin::get_tabs();

        /** Get selected to process tab. */
        $tab = $tabs[ $tab_slug ];

        /** If title enabled. */
        if ( true ===  $tab[ 'show_title' ] ) {

            /** Render Title. */
	        ?>
	        <div class="t42-card-header">
	            <div class="t42-grid t42-grid-small">
	                <div class="t42-width-auto"><h4><?php esc_html_e( $tab[ 'title' ] ); ?></h4></div>

                    <?php if ( Plugin::get_tabs( "$tab_slug:reset_settings" ) ) : ?>
                        <div class="t42-width-expand t42-text-right t42-flex t42-flex-middle t42-flex-right">
                            <a href="#"
                               class="t42-icon-link t42-icon t42-reset-tab-settings-btn"
                               data-tab="<?php esc_attr_e( $tab_slug ); ?>"
                               data-t42-tooltip="<?php esc_html_e( 'Reset Settings to Default', 't42-content-protector' ); ?>"
                               data-t42-icon="icon:fi-updates; ratio: 0.7"></a>
                        </div>
                    <?php endif; ?>

	            </div>
	        </div>
            <?php

        }

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
			    <?php settings_fields( $prefix . 'OptionGroup' ); ?>
			    <?php do_settings_sections( $prefix . 'OptionGroup' ); ?>
		    </div>
	    </div>
	    <?php

    }

    /**
     * Registers a setting and its data.
     * Add a new section to a settings page.
     *
     * @param string $tab_slug - Slug of tub to check.
     *
     * @since 1.0.0
     * @access protected
     *
     * @return void
     **/
    protected function add_settings_base( $tab_slug = null ) {

        /** Foolproof. */
        if ( null === $tab_slug ) { return; }

        $prefix = 't42_content_protector_' . $tab_slug;
        $option_group = $prefix . 'OptionGroup';
        $option_name = $prefix . '_settings';

        /** Status Tab. */
        register_setting( $option_group, $option_name );
        add_settings_section( $prefix . '_section', '', null, $prefix . 'OptionGroup' );

    }

    /**
     * Check if tab is enabled by tab slug.
     *
     * @param string $tab_slug - Tab slug.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return bool
     **/
    public static function is_tab_enabled( $tab_slug ) {

        /** Get all tabs and settings. */
        $tabs = Plugin::get_tabs();

        return isset( $tabs[ $tab_slug ][ 'enabled' ] ) && $tabs[ $tab_slug ][ 'enabled' ];

    }

}
