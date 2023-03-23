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
 * WidgetStartHere class used to implement sidebar widget Start Here.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
class WidgetStartHere extends Widget {

	/**
	 * The one true WidgetStartHere.
	 *
	 * @since 1.0.0
	 * @var WidgetStartHere
	 **/
	private static $instance;

	/**
	 * Sets up a new WidgetStartHere instance.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function __construct() {

		$this->widget_id ='start-here';
		$this->title = esc_html__( 'Start Here', 't42-content-protector' );

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

	    /** Hide widget without content. */
	    if ( ! EnvatoItem::get_instance()->get_widget_start_here() ) { return; }

	    parent::render();

	}

	/**
	 * Render widget content.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	protected function render_content() {

		?>
		<div class="t42-card-body">
			<div class="t42-form-stacked t42-margin-large">

                <?php
                $kses_defaults = wp_kses_allowed_html( 'post' );

                $tags['iframe'] = [
                    'src'             => true,
                    'height'          => true,
                    'width'           => true,
                    'frameborder'     => true,
                    'allowfullscreen' => true,
                    'style'           => true,
                ];

                $allowed_tags = array_merge( $kses_defaults, $tags );

                echo wp_kses( EnvatoItem::get_instance()->get_widget_start_here(), $allowed_tags ); ?>
			</div>
		</div>
		<?php

	}

	/**
	 * Main WidgetStartHere Instance.
	 * Insures that only one instance of WidgetStartHere exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
	 *
	 * @return WidgetStartHere
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

} // End class WidgetStartHere.
