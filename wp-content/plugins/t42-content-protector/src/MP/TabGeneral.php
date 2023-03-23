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
 * SINGLETON: Class used to implement any tab with settings.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class TabGeneral extends Tab {

	/**
	 * The one true TabGeneral.
	 *
     * @since 1.0.0
     * @access private
	 * @var TabGeneral
	 **/
	private static $instance;

    /**
     * Render form with all settings fields.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $tab_slug - Slug of current tab.
     *
     * @return void
     **/
    public function do_settings( $tab_slug ) {

        /** No status tab, nothing to do. */
        if ( ! $this->is_enabled( $tab_slug ) ) { return; }

        /** Render title. */
        $this->render_title( $tab_slug );

        /** Render fields. */
        $this->do_settings_base( $tab_slug );

    }

    /**
     * Generate General Tab.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $tab_slug - Slug of current tab.
     *
     * @return void
     **/
	public function add_settings( $tab_slug ) {

        /** Custom General Tab. */
        $this->add_settings_base( $tab_slug );

		/** Prepare variables. */
		$prefix = 't42_content_protector_' . $tab_slug;
        $page = $prefix . 'OptionGroup';
		$section = $prefix . '_section';

        /** Exit if no fields to process. */
        if ( empty( Plugin::get_tabs()[$tab_slug]['fields'] ) ) { return; }

        $fields = Plugin::get_tabs()[$tab_slug]['fields'];

        /** Create settings for each field. */
        foreach ( $fields as $key => $field ) {

            /** Create field. */
            add_settings_field(
                $key,
                $this->get_label( $field ),
                [ $this, 'create_field' ],
	            $page,
                $section,
                [
                    'key' => $key,
                    'type' => $field[ 'type' ],
                    'tab_slug' => $tab_slug
                ]
            );

        }

	}

	/**
	 * Prepare field label.
	 *
	 * @param array $field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 **/
	private function get_label( $field ) {

	    /** No label or dont show it. */
		if (
            empty( $field['show_label'] ) ||
            ! $field['show_label'] ||
		    empty( $field['label'] ) ||
            'header' === $field['type']
        ) {
		    return '';
        }

		return $field['label'];

    }

    /**
     * Render Settings field.
     *
     * @param array $args - Array of params for render: field key and type.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
	public function create_field( $args = [] ) {

        /** Do we have custom handler for this field type? */
        $handler = $this->get_field_handler( $args );
        if ( is_array( $handler ) && is_callable( $handler ) ) {

            /** Call custom render for field. */
            $handler( $args[ 'key' ], $args[ 'tab_slug' ] );
            return;

        }

        /** In field haven't custom render, check maybe we have standard handler for this field type? */
        if ( ! is_callable( [ $this, 'render_' . $args[ 'type' ] ] ) ) {

            ?>
            <div class="t42-alert-warning" t42-alert="">
                <a class="t42-alert-close" t42-close=""></a>
                <p>
	                <?php
                    esc_html_e( 'Handler for ', 't42-content-protector' );
	                esc_html_e( $args[ 'type' ] );
	                esc_html_e( ' field type not found.', 't42-content-protector' );
	                ?>
                </p>
            </div>
            <?php

            return;

        }

        /** Call render field by type. */
        $this->{'render_' . $args[ 'type' ]}( $args['key'], $args['tab_slug'] );

	}

    /**
     * Return custom handler for field or false.
     *
     * @param array $args - Array of params for render: field key and type.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array|false
     **/
	private function get_field_handler( $args ) {

	    /** Get field. */
        $tabs = Plugin::get_tabs();
        $tab = $tabs[ $args[ 'tab_slug' ] ];
        $fields = $tab[ 'fields' ];
        $field = $fields[ $args[ 'key' ] ];

        if ( ! empty( $field[ 'render' ] ) ) {
            return $field[ 'render' ];
        }

	    return false;

    }

    /**
     * Render Divider field.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $tab_slug - Tab slug to which the field belongs.
     * @param string $key - Field key.
     *
     * @return void
     **/
    public function render_divider( $key, $tab_slug ) {

	    ?><hr class="t42-filed-divider t42-<?php esc_attr_e( Helper::kebab_to_snake( $tab_slug . '-' .$key ) ); ?>"><?php

    }

    /**
     * Render WP Editor field.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @return void
     **/
    public function render_editor( $key, $tab_slug ) {

        /** Prepare general field params. */
	    list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

	    /** Render WP Editor */
	    UI::get_instance()->render_editor(
		    Settings::get_instance()->options[$key],
		    $field,
		    $description,
		    $attr
	    );

    }

    /**
     * Render icon field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_icon( $key, $tab_slug ) {

        /** Prepare general field params. */
	    list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render Icon. */
        UI::get_instance()->render_icon(
            Settings::get_instance()->options[$key],
	        $field,
            $description,
            $attr
        );

    }

    /**
     * Render Colorpicker field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_colorpicker( $key, $tab_slug ) {

        /** Prepare general field params. */
	    list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render colorpicker. */
        UI::get_instance()->render_colorpicker(
            Settings::get_instance()->options[$key],
	        $field,
            $description,
            $attr
        );

    }

    /**
     * Render Textarea field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_textarea( $key, $tab_slug ) {

        /** Prepare general field params. */
	    list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render Textarea. */
        UI::get_instance()->render_textarea(
            Settings::get_instance()->options[$key],
	        $field,
            $description,
            $attr
        );

    }

	/**
	 * Render CSS Editor field.
	 *
	 * @param string $key - Field key.
	 * @param string $tab_slug - Tab slug to which the field belongs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_css_editor( $key, $tab_slug ) {

		/** Prepare general field params. */
		list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

		/** Add code editor for css_editor. */
		wp_enqueue_code_editor( ['type' => 'application/x-httpd-php'] );

		/** Render Textarea. */
		UI::get_instance()->render_css_editor(
			Settings::get_instance()->options[$key],
			$field,
			$description,
			$attr
		);

	}

	/**
     * Render Button field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_button( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        UI::get_instance()->render_button(
	        $field,
            $description,
            $field['icon'],
            $attr
        );

    }

	/**
	 * Render Button Group field.
	 *
	 * @param string $key - Field key.
	 * @param string $tab_slug - Tab slug to which the field belongs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_button_group( $key, $tab_slug ) {

		/** Prepare general field params. */
		list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

		UI::get_instance()->render_button_group(
			$field,
			$description,
			$attr
		);

	}

    /**
     * Render Header field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_header( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render Header. */
        UI::get_instance()->render_header(
	        $field,
            $description,
	        $attr
        );

    }

    /**
     * Render Range Slider field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_range_slider( $key, $tab_slug ) {

        /** Prepare general field params. */
	    list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render slider. */
        UI::get_instance()->render_range_slider(
            Settings::get_instance()->options[$key],
            $field,
            $description,
            $attr
        );

    }

    /**
     * Render Switcher field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_switcher( $key, $tab_slug ) {

        /** Prepare general field params. */
	    list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render switcher. */
        UI::get_instance()->render_switcher(
            Settings::get_instance()->options[$key],
	        $field,
            $description,
            $attr
        );

    }

    /**
     * Render Select field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_select( $key, $tab_slug ) {

        /** Prepare general field params. */
	    list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render select. */
        UI::get_instance()->render_select(
            $field['options'],
            Settings::get_instance()->options[$key], // Selected option.
	        $field,
            $description,
            $attr
        );

    }

    /**
     * Render Text field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
	public function render_text( $key, $tab_slug ) {

	    /** Prepare general field params. */
        list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render input. */
        UI::get_instance()->render_text_input(
            Settings::get_instance()->options[$key],
            $field,
            $description,
            $attr
        );

    }

	/**
	 * Render Number field.
	 *
	 * @param string $key - Field key.
	 * @param string $tab_slug - Tab slug to which the field belongs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_number( $key, $tab_slug ) {

		/** Prepare general field params. */
		list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

		/** Render number input. */
		UI::get_instance()->render_number_input(
			Settings::get_instance()->options[$key],
			$field,
			$description,
			$attr
		);

	}

	/**
	 * Render TouchSpin field.
	 *
	 * @param string $key - Field key.
	 * @param string $tab_slug - Tab slug to which the field belongs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_touch_spin( $key, $tab_slug ) {

		/** Prepare general field params. */
		list( $field, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

		/** Render number input. */
		UI::get_instance()->render_touchspin_input(
			Settings::get_instance()->options[$key],
			$field,
			$description,
			$attr
		);

	}

	/**
	 * Prepare general field params.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param $tab_slug
	 * @param $key
	 *
	 * @return array
	 **/
	public function prepare_general_params( $tab_slug, $key ) {

        /** Get field settings. */
        $field = Plugin::get_tabs()[ $tab_slug ][ 'fields' ][ $key ];

        /** Prepare description and attributes. */
        $description = ! empty( $field[ 'show_description' ] ) ? $field[ 'description' ] : '';
        $attr = $this->prepare_attr( $key, $tab_slug, $field );

        /** Add field slug inside. */
	    $field = ['field_slug' => $key] + $field;

        return [ $field, $description, $attr ];

    }

    /**
     * Prepare array with attributes.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     * @param array $field
     *
     * @since 1.0.0
     * @access private
     *
     * @return array
     **/
    private function prepare_attr( $key, $tab_slug, $field ) {

        $name = 't42_content_protector_' . $tab_slug . '_settings';

        /** Required attributes. */
        $attr = [
            'name'      => $name . '[' . $key . ']',
            'id'        => $name . '-' . $key,
        ];

        /** Multiple fields for colorpicker. */
	    if ( 'colorpicker' === $field['type'] ) {
		    $attr['switcher-name'] = $name . '[' . $key . '_switcher]';
		    $attr['gradient-name'] = $name . '[' . $key . '_gradient]';
        }

	    /** Add placeholder, if not empty. */
	    if ( ! empty( $field[ 'placeholder' ] ) ) {
		    $attr['placeholder'] = $field[ 'placeholder' ];
        }

	    /** Add other attributes, if not empty. */
        if ( ! empty( $field['attr'] ) ) {
            $attr = array_merge( $attr, $field['attr'] );
        }

        return $attr;

    }

	/**
	 * Main TabGeneral Instance.
	 * Insures that only one instance of TabGeneral exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
     *
	 * @return TabGeneral
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

} // End class TabGeneral.
