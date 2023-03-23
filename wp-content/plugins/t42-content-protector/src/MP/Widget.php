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
 * Base methods for Widget Classes.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
abstract class Widget {

	/**
	 * Widget id. Used to generate css selectors.
	 *
	 * @since 1.0.0
	 * @const
	 **/
	protected $widget_id;

	/**
	 * Widget title.
	 *
	 * @since 1.0.0
	 * @var string
	 **/
	protected $title;

	/**
	 * Render widget content.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
	abstract protected function render_content();

	/**
	 * Render widget title.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
	protected function render_title() {

		?>
        <div class="t42-card-header">
            <div class="t42-grid t42-grid-small">
                <div class="t42-width-auto">
                    <h4><?php echo wp_kses_post( $this->title ); ?></h4>
                </div>
                <div class="t42-width-expand t42-text-right panel-icons">
                    <a id="t42-widget-<?php esc_attr_e( $this->widget_id ); ?>-btn" href="#" class="t42-widget-close-btn t42-icon-link t42-icon" title="<?php esc_html_e( 'Close', 't42-content-protector' ); ?>" data-t42-tooltip="" data-t42-icon="icon: close"></a>
                </div>
            </div>
        </div>
		<?php

	}

	/**
	 * Render widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render() {

		/** Exit, if widget hidden for current user. */
		if ( ! $this->is_visible( $this->widget_id ) ) { return; }

		?><div class="t42-card t42-card-default t42-card-small t42-grid-margin-medium t42-widget-<?php esc_html_e( $this->widget_id ); ?>"><?php

		/** Render widget title. */
		$this->render_title();

		/** Render widget content. */
		$this->render_content();

		?></div><?php

	}

	/**
	 * Return true if current widget visible for current user.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean
	 **/
	protected function is_visible( $widget_id ) {

        /** Prepare 'plugin_slug_widget_id_{user_id}' option name to store widget status. */
		$widget_opt = Plugin::get_snake_slug() . '_widget_' . $widget_id . '_' . get_current_user_id();

		/** Is widget hidden? */
		$hidden = get_option( $widget_opt );

		/** Widget hidden. */
		if ( $hidden ) { return false; }

		/** Widget visible. */
		return true;

    }

}
