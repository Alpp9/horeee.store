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

use Cassandra\Set;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to render fields.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class UI {

	/**
	 * The one true UI.
	 *
	 * @var UI
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Render type="text" input field.
	 *
	 * @param integer $value - Input value.
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
     *
	 * @return void
     *
	 * @noinspection PhpUnusedParameterInspection
	 **/
	public function render_text_input( $value, $field = null, $description = '',  $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-text-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$placeholder = isset( $attributes['placeholder'] ) ? $attributes['placeholder'] : '';

		$prefix = isset( $attributes['prefix'] ) ? $attributes['prefix'] : '';
		$suffix = isset( $attributes['suffix'] ) ? $attributes['suffix'] : '';
		$is_input_group = ! ( empty( $prefix ) && empty( $suffix ) );

		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		if ( $is_input_group ) {
		    $box_classes[] = 't42-input-group';
			$box_classes[] = ! empty( $prefix ) ? 't42-has-prefix' : '';
			$box_classes[] = ! empty( $suffix ) ? 't42-has-suffix' : '';
		}
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-text-input-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">

	            <?php if ( ! empty( $prefix ) ) : ?>
                    <div class="t42-input-group-prepend">
                        <div class="t42-input-group-text">
				            <?php esc_html_e( $prefix ); ?>
                        </div>
                    </div>
	            <?php endif; ?>

                <label class="t42-form-label t42-width-expand" for="<?php esc_attr_e( $id ); ?>">
                    <input class="t42-input"
                           id="<?php esc_attr_e( $id ); ?>"
                           type="text"
                           name="<?php esc_attr_e( $name ); ?>"
                           placeholder="<?php esc_attr_e( $placeholder ); ?>"
                           value="<?php esc_attr_e( $value ); ?>"
                    >
                </label>

                <?php if ( ! empty( $suffix ) ) : ?>
                    <div class="t42-input-group-append">
                        <div class="t42-input-group-text">
                            <?php esc_html_e( $suffix ); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

	        <?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
                    <div>
		                <?php echo wp_kses_post( $description ); ?>
                    </div>
                </div>
	        <?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render type="number" input field.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $value        - Input value.
	 * @param array   $field        - All field params.
	 * @param string  $description  - Field description. Optional.
	 * @param array   $attributes   - Additional attributes. Optional.
	 *
	 * @return void
	 *
	 * @noinspection PhpUnusedParameterInspection
     **/
	public function render_number_input( $value, $field = null, $description = '',  $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-number-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$placeholder = isset( $attributes['placeholder'] ) ? $attributes['placeholder'] : '';

		$min = isset( $attributes['min'] ) ? $attributes['min'] : '';
		$max = isset( $attributes['max'] ) ? $attributes['max'] : '';
		$step = isset( $attributes['step'] ) ? $attributes['step'] : '';

		$prefix = isset( $attributes['prefix'] ) ? $attributes['prefix'] : '';
		$suffix = isset( $attributes['suffix'] ) ? $attributes['suffix'] : '';
		$is_input_group = ! ( empty( $prefix ) && empty( $suffix ) );

		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		if ( $is_input_group ) {
			$box_classes[] = 't42-input-group';
			$box_classes[] = ! empty( $prefix ) ? 't42-has-prefix' : '';
			$box_classes[] = ! empty( $suffix ) ? 't42-has-suffix' : '';
		}
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-number-input-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">

				<?php if ( ! empty( $prefix ) ) : ?>
                    <div class="t42-input-group-prepend">
                        <div class="t42-input-group-text">
							<?php esc_html_e( $prefix ); ?>
                        </div>
                    </div>
				<?php endif; ?>

                <label class="t42-form-label t42-width-auto" for="<?php esc_attr_e( $id ); ?>">
                    <input type="number"
                           class="t42-input t42-width-auto"
                           id="<?php esc_attr_e( $id ); ?>"
                           name="<?php esc_attr_e( $name ); ?>"
                           placeholder="<?php esc_attr_e( $placeholder ); ?>"
                           value="<?php esc_attr_e( $value ); ?>"
                           <?php if ( ! empty( $min ) ) { ?> min="<?php esc_attr_e( $min ); ?>"<?php } ?>
	                       <?php if ( ! empty( $max ) ) { ?> max="<?php esc_attr_e( $max ); ?>"<?php } ?>
	                       <?php if ( ! empty( $step ) ) { ?> step="<?php esc_attr_e( $step ); ?>"<?php } ?>
                    >
                </label>

				<?php if ( ! empty( $suffix ) ) : ?>
                    <div class="t42-input-group-append">
                        <div class="t42-input-group-text">
							<?php esc_html_e( $suffix ); ?>
                        </div>
                    </div>
				<?php endif; ?>
            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
                    <div>
		                <?php echo wp_kses_post( $description ); ?>
                    </div>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render TouchSpin input field.
     * @see https://github.com/istvan-ujjmeszaros/bootstrap-touchspin
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $value        - Input value.
	 * @param array   $field        - All field params.
	 * @param string  $description  - Field description. Optional.
	 * @param array   $attributes   - Additional attributes. Optional.
	 *
	 * @return void
	 *
	 * @noinspection PhpUnusedParameterInspection
	 **/
	public function render_touchspin_input( $value, $field = null, $description = '',  $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-touchspin-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$placeholder = isset( $attributes['placeholder'] ) ? $attributes['placeholder'] : '';

		$min = isset( $attributes['min'] ) ? $attributes['min'] : '0';
		$max = isset( $attributes['max'] ) ? $attributes['max'] : '100';
		$step = isset( $attributes['step'] ) ? $attributes['step'] : '1';
		$decimals = isset( $attributes['decimals'] ) ? $attributes['decimals'] : '0';

		$prefix = isset( $attributes['prefix'] ) ? $attributes['prefix'] : '';
		$suffix = isset( $attributes['suffix'] ) ? $attributes['suffix'] : '';
		$is_input_group = ! ( empty( $prefix ) && empty( $suffix ) );

		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		if ( $is_input_group ) {
			$box_classes[] = 't42-input-group';
			$box_classes[] = ! empty( $prefix ) ? 't42-has-prefix' : '';
			$box_classes[] = ! empty( $suffix ) ? 't42-has-suffix' : '';
		}
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-touchspin-input-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">

                <label class="t42-form-label t42-width-auto" for="<?php esc_attr_e( $id ); ?>">
                    <input type="text"
                           class="t42-input t42-width-auto"
                           id="<?php esc_attr_e( $id ); ?>"
                           name="<?php esc_attr_e( $name ); ?>"
                           placeholder="<?php esc_attr_e( $placeholder ); ?>"
                           value="<?php esc_attr_e( $value ); ?>"
                           <?php if ( ! empty( $min ) ) { ?> data-min="<?php esc_attr_e( $min ); ?>"<?php } ?>
						   <?php if ( ! empty( $max ) ) { ?> data-max="<?php esc_attr_e( $max ); ?>"<?php } ?>
                           <?php if ( ! empty( $step ) ) { ?> data-step="<?php esc_attr_e( $step ); ?>"<?php } ?>
                           <?php if ( ! empty( $decimals ) ) { ?> data-decimals="<?php esc_attr_e( $decimals ); ?>"<?php } ?>
	                       <?php if ( ! empty( $prefix ) ) { ?> data-prefix="<?php esc_attr_e( $prefix ); ?>"<?php } ?>
	                       <?php if ( ! empty( $suffix ) ) { ?> data-suffix="<?php esc_attr_e( $suffix ); ?>"<?php } ?>
                    >
                </label>

            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
                    <div>
		                <?php echo wp_kses_post( $description ); ?>
                    </div>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render Header filed with description.
	 *
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_header( $field, $description = '', $attributes = [] ) {

		/** Prepare html attributes. */
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';

		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-header-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">
                <?php $this->render_h_tag( $field ); ?>
                <?php if ( $description ) : ?>
                    <p><?php echo wp_kses_post( $description ); ?></p>
                <?php endif; ?>
            </div>
        </div>
		<?php

	}

	/**
     * Render header inside <h1>-<h6> tag.
     *
	 * @param $field
     *
     * @return void
	 **/
	private function render_h_tag( $field ) {

		$tag = ! empty( $field['level'] ) ? $field['level'] : 'h3';

		?><<?php esc_html_e( $tag ); ?>><?php echo wp_kses_post( $field['header'] ); ?></<?php esc_html_e( $tag ); ?>><?php

    }

	/**
	 * Render button field.
	 *
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_button( $field, $description = '', $icon = '',  $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-button-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';

		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-button-field">
            <div class="<?php esc_attr_e( $box_classes ); ?>">
                <button class="t42-button <?php esc_attr_e( $class ); ?>"
                        id="<?php esc_attr_e( $id ); ?>"
                        name="<?php esc_attr_e( $name ); ?>"
                >
                    <?php if ( ! empty( $icon ) ) : ?>
                        <span data-t42-icon="icon: <?php esc_attr_e( $icon ); ?>" class="t42-margin-small-right t42-icon"></span>
                    <?php endif; ?>
                    <span class="t42-text"><?php echo wp_kses_post( $field['button_text'] ); ?></span>
                </button>
            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
                    <div>
		                <?php echo wp_kses_post( $description ); ?>
                    </div>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render button group field.
	 *
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_button_group( $field, $description = '', $attributes = [] ) {

	    /** Array of buttons. */
	    $group = $field['group'];

		/** Prepare general html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-btn-group-' . md5( uniqid( mt_rand(), true ) );

		/** Prepare box classes. */
		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-button-group-field">
            <div class="<?php esc_attr_e( $box_classes ); ?>">
                <div class="t42-button-group">
                    <?php foreach ( $group as $i => $button ) : ?>
                        <?php
                        $class = isset( $button['attr']['class'] ) ? $button['attr']['class'] : '';
	                    $icon = isset( $button['icon'] ) ? $button['icon'] : '';
                        ?>
                        <button class="t42-button <?php esc_attr_e( $class ); ?>"
                                id="<?php esc_attr_e( $id . '-' . $i ); ?>"
                        >
	                        <?php if ( ! empty( $icon ) ) : ?>
                                <span data-t42-icon="icon: <?php esc_attr_e( $icon ); ?>" class="t42-margin-small-right t42-icon"></span>
	                        <?php endif; ?>
                            <span class="t42-text"><?php echo wp_kses_post( $button['button_text'] ); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
                    <div>
		                <?php echo wp_kses_post( $description ); ?>
                    </div>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render switcher field.
	 *
     * @param string $value - Value of field.
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
     *
	 * @noinspection PhpUnusedParameterInspection
	 **/
	public function render_switcher( $value, $field, $description = '', $attributes = [] ) {

		/** Prepare general html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-switcher-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$placeholder = isset( $attributes['placeholder'] ) ? $attributes['placeholder'] : '';

		/** Prepare box classes. */
		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
		<div class="t42-margin t42-switcher-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">
                <input type="hidden"
                       id="<?php esc_attr_e( $id ); ?>-i"
                       name="<?php esc_attr_e( $name ); ?>"
                       value="off"
                >
                <label class="t42-switch" for="<?php esc_attr_e( $id ); ?>">
                    <input type="checkbox"
                           id="<?php esc_attr_e( $id ); ?>"
                           name="<?php esc_attr_e( $name ); ?>"
                           <?php esc_attr_e( ( $value === 'on' ) ? 'checked' : '' ); ?>
                    >
                    <span class="t42-switch-slider"></span>
	                <?php if ( $placeholder ) : ?>
                        <div class="t42-switch-placeholder"><?php esc_html_e( $placeholder ); ?></div>
	                <?php endif; ?>
                </label>
            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
                    <div>
		                <?php echo wp_kses_post( $description ); ?>
                    </div>
                </div>
			<?php endif; ?>
        </div>
        <?php

    }

	/**
	 * Render textarea field.
	 *
	 * @param string $value - Value of field.
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
     *
	 * @noinspection PhpUnusedParameterInspection
	 **/
	public function render_textarea( $value, $field, $description = '', $attributes = [] ) {

		/** Prepare general html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-textarea-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$placeholder = isset( $attributes['placeholder'] ) ? $attributes['placeholder'] : '';
		$rows = isset( $attributes['rows'] ) ? $attributes['rows'] : '';

		/** Prepare box classes. */
		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-textarea-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">
                <textarea id="<?php esc_attr_e( $id ); ?>"
                          name="<?php esc_attr_e( $name ); ?>"
                          class="t42-textarea"
                          rows="<?php esc_attr_e( $rows ); ?>"
                          placeholder="<?php esc_attr_e( $placeholder ); ?>"
                ><?php echo esc_textarea( $value ); ?></textarea>
            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
                    <div>
		                <?php echo wp_kses_post( $description ); ?>
                    </div>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render CSS editor field.
	 *
	 * @param string $value - Value of field.
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
     *
	 * @noinspection PhpUnusedParameterInspection
	 **/
	public function render_css_editor( $value, $field, $description = '', $attributes = [] ) {

		/** Prepare general html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-css-editor-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$placeholder = isset( $attributes['placeholder'] ) ? $attributes['placeholder'] : '';
		$rows = isset( $attributes['rows'] ) ? $attributes['rows'] : '';

		/** Prepare box classes. */
		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-css-editor-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">
                <textarea id="<?php esc_attr_e( $id ); ?>"
                          name="<?php esc_attr_e( $name ); ?>"
                          class="t42-css-editor"
                          rows="<?php esc_attr_e( $rows ); ?>"
                          placeholder="<?php esc_attr_e( $placeholder ); ?>"
                ><?php echo esc_textarea( $value ); ?></textarea>
            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
                    <div>
	                    <?php echo wp_kses_post( $description ); ?>
                    </div>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render Range Slider field.
	 *
	 * @param string $value - Value of field.
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_range_slider( $value, $field, $description = '', $attributes = [] ) {

		/** Prepare general html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-range-slider-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';

		$min = isset( $field['min'] ) ? $field['min'] : 0;
		$max = isset( $field['max'] ) ? $field['max'] : 100;
		$step = isset( $field['step'] ) ? $field['step'] : 1;
		$prefix = isset( $field['prefix'] ) ? $field['prefix'] : '';
        $postfix = isset( $field['postfix'] ) ? $field['postfix'] : '';

		/** Prepare box classes. */
		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-range-slider-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">
                <label>
                    <input type="text"
                           id="<?php esc_attr_e( $id ); ?>"
                           name="<?php esc_attr_e( $name ); ?>"
                           class="t42-range-slider"
                           value="<?php esc_attr_e( $value ); ?>"
                           data-type="single"
                           data-min="<?php esc_attr_e( $min ); ?>"
                           data-max="<?php esc_attr_e( $max ); ?>"
                           data-step="<?php esc_attr_e( $step ); ?>"
                           data-keyboard="true"
                           data-grid="true"
                           data-skin="round"
                           data-prefix="<?php esc_attr_e( $prefix ); ?>"
                           data-postfix="<?php esc_attr_e( $postfix ); ?>"
                    />
                </label>
            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
					<?php echo wp_kses_post( $description ); ?>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render Colorpicker field.
	 *
	 * @param string $value - Value of field.
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_colorpicker( $value, $field, $description = '', $attributes = [] ) {

		/** Prepare general html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-colorpicker-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';

		/** Shorthand for plugin options. */
		$options = Settings::get_instance()->options;

		$gradient_mode = ! empty( $field['gradient_mode'] ) && $field['gradient_mode'];

		$switcher_id = $id . '_switcher';
		$switcher_name = isset( $attributes['switcher-name'] ) ? $attributes['switcher-name'] : '';
		$switcher_opt = $field['field_slug'] . '_switcher';
		$switcher_value = isset( $options[ $switcher_opt ]  ) ? $options[ $switcher_opt ] : '';

		$gradient_id = $id . '_gradient';
        $gradient_name = isset( $attributes['gradient-name'] ) ? $attributes['gradient-name'] : '';
		$gradient_opt = $field['field_slug'] . '_gradient';
        $gradient_value = isset( $options[ $gradient_opt ]  ) ? $options[ $gradient_opt ] : '';

		/** Prepare box classes. */
		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';

		if ( $gradient_mode ) {
			$box_classes[] = 't42-gradient-mode';
        }

		if ( 'on' === $switcher_value && $gradient_mode ) {
			$box_classes[] = 't42-gradient-mode-on';
		}

		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-colorpicker-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">
                <div class="t42-colorpicker-input-box">
                    <label>
                        <input type="text"
                               id="<?php esc_attr_e( $id ); ?>"
                               name="<?php esc_attr_e( $name ); ?>"
                               class="t42-colorpicker"
                               value="<?php esc_attr_e( $value ); ?>"
                        />
                    </label>
                </div>

	            <?php if ( $gradient_mode ) : ?>
                    <div class="t42-gradient-input-box">
                        <div class="t42-gradient-preview-box">
                            <div class="t42-gradient" style="background: <?php esc_attr_e( $gradient_value ); ?>"></div>
                        </div>
                        <label>
                            <input type="text"
                                   id="<?php esc_attr_e( $gradient_id ); ?>"
                                   name="<?php esc_attr_e( $gradient_name ); ?>"
                                   class="t42-gradientpicker"
                                   value="<?php esc_attr_e( $gradient_value ); ?>"
                            />
                        </label>
                    </div>

                    <div class="t42-gradient-switcher-field">
                        <input type="hidden"
                               id="<?php esc_attr_e( $switcher_id ); ?>-i"
                               name="<?php esc_attr_e( $switcher_name ); ?>"
                               value="off"
                        >
                        <label class="t42-switch" for="<?php esc_attr_e( $switcher_id ); ?>">
                            <input type="checkbox"
                                   id="<?php esc_attr_e( $switcher_id ); ?>"
                                   name="<?php esc_attr_e( $switcher_name ); ?>"
					               <?php esc_attr_e( ( $switcher_value === 'on' ) ? 'checked' : '' ); ?>
                            >
                            <span class="t42-switch-slider"></span>
                            <span class="t42-switch-placeholder">
                                <?php esc_html_e( 'Gradient Mode', 't42-content-protector' ); ?>
                            </span>
                        </label>
                    </div>
                    <div class="t42-gpickr-box">
                        <div class="t42-gpickr" id="<?php esc_attr_e( $id . '-gpickr-el' ); ?>"></div>
                    </div>
	            <?php endif; ?>

            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
					<?php echo wp_kses_post( $description ); ?>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render Icon field.
	 *
	 * @param string $value - Value of field.
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_icon( $value, $field, $description = '', $attributes = [] ) {

	    /** Enqueue WordPress Image library. */
		wp_enqueue_media();

		/** Prepare general html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-icon-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';

		/** Prepare box classes. */
		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		/** Get icon folder URL. */
		$icon_folder_url = Plugin::get_url() . 'images/icons/';

		/** Collect icons from all .json to one array. */
		$icons_arr = [];
		foreach ( $field['meta'] as $meta_json ) {

			/** Load icons from meta.json */
			$json = wp_remote_get( $icon_folder_url . $meta_json, ['timeout' => 15] );
			if ( is_wp_error( $json ) ){
				echo wp_kses_post( $json->get_error_message() );
			}

			/** Decode icons to array. */
			$meta_arr = json_decode( $json['body'], true );

			/** Collect icons from all .json to one array. */
			$icons_arr[] = $meta_arr[0];
		}

		/** Generate big json with all icons. */
		$icons_json = json_encode( $icons_arr );

		?>
        <div class="t42-margin t42-icon-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">
                <label>
                    <input type="hidden"
                           id="<?php esc_attr_e( $id ); ?>"
                           name="<?php esc_attr_e( $name ); ?>"
                           class="t42-icon"
                           value="<?php esc_attr_e( $value ); ?>"
                    />
                </label>

	            <?php
                /** Get icon src. */
                if ( is_numeric( $value ) ) {
		            $ico_attributes = wp_get_attachment_image_src( $value ); // Get icon by id.
		            $src = $ico_attributes[0];
	            } elseif( $value ) {
		            $src = $icon_folder_url . $value;
	            } else {
		            $src = $value;
	            }
                ?>

                <div class="t42-icon-field-img-box <?php echo ( $src ) ? 't42-with-image' : ''; ?>">
                    <div class="t42-icon-field-image">
			            <?php if ( $src ) : ?>
                            <img src="<?php esc_attr_e( $src ); ?>" class="svg" alt="">
			            <?php endif; ?>
                    </div>
                    <div class="t42-icon-field-remove" title="<?php esc_html_e( 'Remove', 't42-content-protector' ); ?>">
                        <span data-t42-icon="icon: close" class="t42-icon"></span>
                    </div>
                    <button class="t42-icon-field-library-btn t42-button t42-button-default"
                            data-library="<?php esc_attr_e( $icons_json ); ?>"
                            data-folder="<?php esc_attr_e( $icon_folder_url ); ?>"
                    >
	                    <?php esc_html_e( 'Icon Library', 't42-content-protector' ); ?>
                    </button>
                    <button class="t42-icon-field-upload-btn t42-button t42-button-default">
                        <?php esc_html_e( 'Upload SVG', 't42-content-protector' ); ?>
                    </button>
                </div>

            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
					<?php echo wp_kses_post( $description ); ?>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render WP Editor field.
	 *
	 * @param string $value - Value of field.
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
     *
	 * @noinspection PhpUnusedParameterInspection
	 **/
	public function render_editor( $value, $field, $description = '', $attributes = [] ) {

		/** Prepare general html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-editor-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$textarea_rows = isset( $attributes['textarea_rows'] ) ? $attributes['textarea_rows'] : '3';

		/** Prepare box classes. */
		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-editor-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">
                <?php wp_editor( $value, $id, [ 'textarea_rows' => $textarea_rows, 'textarea_name' => $name ] ); // Render WP Editor. ?>
            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
					<?php echo wp_kses_post( $description ); ?>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render activate input with button field.
	 *
	 * @param integer $value - Input value.
	 * @param array $field - Field.
	 * @param string $description
	 * @param array $attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 *
	 * @noinspection PhpUnusedParameterInspection
	 **/
	public function render_activate_input( $value, $field = null, $description = '',  $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-activate-input-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$placeholder = isset( $attributes['placeholder'] ) ? $attributes['placeholder'] : '';

		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';

		if ( PluginActivation::get_instance()->is_activated() ) {
			$box_classes[] = 't42-activated-plugin';
        }

		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
        <div class="t42-margin t42-activate-input-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">

                <label class="t42-form-label t42-width-expand" for="<?php esc_attr_e( $id ); ?>">
                    <input class="t42-input"
                           id="<?php esc_attr_e( $id ); ?>"
                           type="text"
                           name="<?php esc_attr_e( $name ); ?>"
                           placeholder="<?php esc_attr_e( $placeholder ); ?>"
                           value="<?php esc_attr_e( $value ); ?>"
                    >
                    <button class="t42-button t42-button-default t42-activate-btn t42-waves-effect t42-waves-dark">
                        <?php esc_html_e( 'Activate', 't42-content-protector' ); ?>
                    </button>
                </label>

            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
					<?php echo wp_kses_post( $description ); ?>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Render select field.
	 *
	 * @param array $options - Options for select.
	 * @param array $selected - List of selected values.
	 * @param array $field - Field.
	 * @param string $description - Short field description.
	 * @param array $attributes - Additional attributes for select: id, name, class. Optional.
	 *
	 * @since 1.0.0
	 * @access public
     *
	 * @return void
	 **/
	public function render_select( $options, $selected, $field, $description = '',  $attributes = [] ) {

	    /** Convert single value to array. */
	    if ( ! is_array( $selected ) ) {
		    $selected = [$selected];
        }

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 't42-select-' . md5( uniqid( mt_rand(), true ) );
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$multiple = isset( $attributes['multiple'] ) ? $attributes['multiple'] : '';
		$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		$allow_clear = isset( $attributes['allow_clear'] ) ? $attributes['allow_clear'] : '';

		/** Fix field name to save multiple values. */
		if ( 'multiple' === $multiple ) {
			$name .= '[]';
		}

		/** Prepare box classes. */
		$box_classes = [];
		$box_classes[] = 't42-form-controls';
		$box_classes[] = 't42-width-3-4@m';
		$box_classes = implode( ' ', $box_classes );
		$box_classes = preg_replace( '/\s+/', ' ', $box_classes );

		?>
		<div class="t42-margin t42-select-field <?php esc_attr_e( $class ); ?>">
            <div class="<?php esc_attr_e( $box_classes ); ?>">
                <label class="t42-form-label t42-width-expand" for="<?php esc_attr_e( $id ); ?>">
                    <select id="<?php esc_attr_e( $id ); ?>"
                            name="<?php esc_attr_e( $name ); ?>"
                            class="t42-select2 t42-width-expand"
                            data-placeholder="<?php esc_attr_e( $placeholder ); ?>"
                            data-allow-clear="<?php esc_attr_e( $allow_clear ); ?>"
                            <?php echo ( $multiple ? 'multiple=""' : '' ); ?>
                    >
		                <?php foreach ( $options as $key => $value ) : ?>
			                <?php $is_selected = ( in_array( $key, $selected, true ) ) ? 'selected=""' : ''; ?>
			                <option value="<?php esc_attr_e( $key ); ?>" <?php esc_attr_e( $is_selected ); ?>><?php esc_html_e( $value ); ?></option>
		                <?php endforeach; ?>
                    </select>
                </label>
            </div>

			<?php if ( $description ) : ?>
                <div class="t42-flex t42-flex-middle t42-margin-small-top t42-text-muted t42-description">
					<?php echo wp_kses_post( $description ); ?>
                </div>
			<?php endif; ?>
        </div>
		<?php

	}

	/**
	 * Main UI Instance.
	 *
	 * Insures that only one instance of UI exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
     *
	 * @return UI
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

} // End Class UI.

