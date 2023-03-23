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

use WP_Query;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement Assignments tab on plugin settings page.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class TabAssignments extends Tab {

	/**
	 * Slug of current tab.
	 *
	 * @const TAB_SLUG
	 **/
	const TAB_SLUG = 'assignments';

	/**
	 * The one true TabAssignments.
	 *
	 * @var TabAssignments
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new TabAssignments instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Add admin javascript. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

	}

	/**
	 * Generate Assignments Tab.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function add_settings() {

		/** Assignments Tab. */
		$this->add_settings_base( self::TAB_SLUG );

	}

	/**
	 * Render form with all settings fields.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function do_settings() {

		/** No tab, nothing to do. */
		if ( ! $this->is_enabled( self::TAB_SLUG ) ) { return; }

		/** Render title. */
		$this->render_title( self::TAB_SLUG );

		/** Render fields. */
		$this->do_settings_base( self::TAB_SLUG );

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
				<?php

                settings_fields( $prefix . 'OptionGroup' );
				do_settings_sections( $prefix . 'OptionGroup' );

                /** Render Assignments. */
                $this->render_assignments();

                ?>
            </div>
        </div>
		<?php

	}

	/**
	 * Add JS for admin area.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function admin_scripts() {

	    /** Get current screen object. */
		$screen = get_current_screen();
		if ( null === $screen ) { return; }

		/** Add styles only on Plugin Settings page. */
		if ( in_array( $screen->base, Plugin::get_menu_bases(), true ) ) {

			/** MP periodpicker.jquery.js. */
			AdminScripts::get_instance()->enqueue_script( 'periodpicker.jquery', true, ['jquery'] );

			/** MP assignments.js. */
			AdminScripts::get_instance()->enqueue_script( 'assignments', true, ['jquery', 't42-content-protector-mp-admin'] );

			/** Add code editor for Custom PHP. */
			wp_enqueue_code_editor( ['type' => 'application/x-httpd-php'] );

		}

	}

	/**
	 * Render Assignments field.
	 *
	 * @since 1.0.0
	 **/
	public function render_assignments() {

		$assignments_settings = Settings::get_instance()->options['assignments'];

		/**
		 * Output options list for select
		 */
		$options  = [];
		$defaults = [
			'search' => 'Search'
		];

		$selected = is_array( $options ) ? $options : [ '*' ];

		if ( count( $selected ) > 1 && in_array( '*', $selected, false ) ) {
			$selected = [ '*' ];
		}

		// set default options
		foreach ( $defaults as $val => $label ) {
			$attributes = in_array( $val, $selected, false ) ? [
				'value'    => $val,
				'selected' => 'selected'
			] : [ 'value' => $val ];
			$options[]  = sprintf( '<option value="%s">%s</option>', $attributes["value"], $label );
		}

		// set pages
		if ( $pages = get_pages() ) {
			$options[] = '<optgroup label="Pages">';

			array_unshift( $pages, (object) [ 'post_title' => 'Pages (All)' ] );

			foreach ( $pages as $page ) {
				$val        = isset( $page->ID ) ? 'page-' . $page->ID : 'page';
				$attributes = in_array( $val, $selected, false ) ? [
					'value'    => $val,
					'selected' => 'selected'
				] : [ 'value' => $val ];
				$options[]  = sprintf( '<option value="%s">%s</option>', $attributes["value"], $page->post_title );
			}

			$options[] = '</optgroup>';
		}

		// set posts
		$options[] = '<optgroup label="Post">';
		foreach ( [ 'home', 'single', 'archive' ] as $view ) {
			$val        = $view;
			$attributes = in_array( $val, $selected, false ) ? [
				'value'    => $val,
				'selected' => 'selected'
			] : [ 'value' => $val ];
			$options[]  = sprintf( '<option value="%s">%s (%s)</option>', $attributes["value"], 'Post', ucfirst( $view ) );
		}
		$options[] = '</optgroup>';

		// set custom post types
		foreach ( array_keys( get_post_types( ['_builtin' => false ] ) ) as $post_type ) {
			$obj   = get_post_type_object( $post_type );
			if ( null === $obj ) { continue; }

			$label = ucfirst( $post_type );

			if ( $obj->publicly_queryable ) {
				$options[] = '<optgroup label="' . $label . '">';

				foreach ( [ 'single', 'archive', 'search' ] as $view ) {
					$val        = $post_type . '-' . $view;
					$attributes = in_array( $val, $selected, false ) ? [
						'value'    => $val,
						'selected' => 'selected'
					] : [ 'value' => $val ];
					$options[]  = sprintf( '<option value="%s">%s (%s)</option>', $attributes["value"], $label, ucfirst( $view ) );
				}

				$options[] = '</optgroup>';
			}
		}

		// set categories
		foreach ( array_keys( get_taxonomies() ) as $tax ) {

			if ( in_array( $tax, [ "post_tag", "nav_menu" ] ) ) {
				continue;
			}

			if ( $categories = get_categories( [ 'taxonomy' => $tax ] ) ) {
				$options[] = '<optgroup label="Categories (' . ucfirst( str_replace( [
						"_",
						"-"
					], " ", $tax ) ) . ')">';

				foreach ( $categories as $category ) {
					$val        = 'cat-' . $category->cat_ID;
					$attributes = in_array( $val, $selected, false ) ? [
						'value'    => $val,
						'selected' => 'selected'
					] : [ 'value' => $val ];
					$options[]  = sprintf( '<option value="%s">%s</option>', $attributes["value"], $category->cat_name );
				}

				$options[] = '</optgroup>';
			}
		}

		?>

        <input type='hidden' class="t42-width-1-1" id="t42-assignInput"
               name='t42_content_protector_assignments_settings[assignments]'
               value='<?php echo esc_attr( $assignments_settings ); ?>'>

        <div id="t42-assign-box">

            <div class="t42-all-fields">
                <div class="t42-alert">
                    <?php esc_html_e( 'By selecting the specific assignments you can limit where plugin should or shouldn\'t be published. To have it published on all pages, simply do not specify any assignments.' ); ?>
                </div>

                <div class="t42-panel t42-panel-box t42-matching-method t42-margin <?php $this->is_hidden( 'matching_method' ); ?>">
                    <h4><?php esc_html_e( 'Matching Method', 't42-content-protector' ); ?></h4>
                    <p><?php esc_html_e( 'Should all or any assignments be matched?', 't42-content-protector' ); ?></p>
                    <div class="t42-button-group t42-matchingMethod" data-t42-button-radio="">
                        <button class="t42-button t42-button-success t42-button-small t42-all t42-active"><?php esc_html_e( 'ALL', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-success t42-button-small t42-any"><?php esc_html_e( 'ANY', 't42-content-protector' ); ?></button>
                    </div>
                    <p>
                        <strong><?php esc_html_e( 'ALL', 't42-content-protector' ); ?></strong><?php esc_html_e( ' — Will be published if ', 't42-content-protector' ); ?>
                        <strong><?php esc_html_e( 'ALL', 't42-content-protector' ); ?></strong><?php esc_html_e( ' of below assignments are matched.', 't42-content-protector' ); ?>
                        <br>
                        <strong><?php esc_html_e( 'ANY', 't42-content-protector' ); ?></strong><?php esc_html_e( ' — Will be published if ', 't42-content-protector' ); ?>
                        <strong><?php esc_html_e( 'ANY', 't42-content-protector' ); ?></strong><?php esc_html_e( ' (one or more) of below assignments are matched.', 't42-content-protector' ); ?>
                        <br>
                    </p>
                </div>

                <div class="t42-panel t42-panel-box t42-wp-content t42-margin <?php $this->is_hidden( 'wordpress_content' ); ?>">

                    <h4><?php esc_html_e( 'WordPress Content', 't42-content-protector' ); ?></h4>

                    <div class="t42-button-group" data-t42-button-radio="">
                        <button class="t42-button t42-button-primary t42-button-small t42-ignore t42-active"><?php esc_html_e( 'Ignore', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-success t42-button-small t42-include"><?php esc_html_e( 'Include', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-danger t42-button-small t42-exclude"><?php esc_html_e( 'Exclude', 't42-content-protector' ); ?></button>
                    </div>

                    <div class="t42-wp-content-box">
                        <p class="t42-margin-top">
							<?php esc_html_e( 'Select on what page types or categories the assignment should be active.', 't42-content-protector' ); ?>
                        </p>
                        <label>
                            <select class="wp-content t42-select2 t42-width-expand" data-placeholder="<?php esc_attr_e( 'Select some options', 't42-content-protector' ); ?>" multiple="multiple">
                                <?php echo implode( "", $options ) ?>
                            </select>
                        </label>
                    </div>

                </div>

                <div class="t42-panel t42-panel-box t42-home-page t42-margin <?php $this->is_hidden( 'home_page' ); ?>">

                    <h4><?php esc_html_e( 'Home Page', 't42-content-protector' ); ?></h4>

                    <div class="t42-button-group" data-t42-button-radio="">
                        <button class="t42-button t42-button-primary t42-button-small t42-ignore t42-active"><?php esc_html_e( 'Ignore', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-success t42-button-small t42-include"><?php esc_html_e( 'Include', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-danger t42-button-small t42-exclude"><?php esc_html_e( 'Exclude', 't42-content-protector' ); ?></button>
                    </div>

                </div>

                <div class="t42-panel t42-panel-box t42-menu-items t42-margin <?php $this->is_hidden( 'menu_items' ); ?>">
                    <h4><?php esc_html_e( 'Menu Items', 't42-content-protector' ); ?></h4>
                    <div class="t42-button-group" data-t42-button-radio="">
                        <button class="t42-button t42-button-primary t42-button-small t42-ignore t42-active"><?php esc_html_e( 'Ignore', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-success t42-button-small t42-include"><?php esc_html_e( 'Include', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-danger t42-button-small t42-exclude"><?php esc_html_e( 'Exclude', 't42-content-protector' ); ?></button>
                    </div>

                    <div class="t42-menuitems-selection">
                        <p class="t42-margin-top"><?php esc_html_e( 'Select the menu items to assign to.', 't42-content-protector' ); ?></p>
                        <label>
                            <select class="menuitems t42-select2 t42-width-expand" data-placeholder="<?php esc_attr_e( 'Select some options', 't42-content-protector' ); ?>" multiple="">
                                <?php
                                /** Get all menus */
                                $menus = get_terms( 'nav_menu', ['hide_empty' => true] );
                                foreach ( $menus as $menu ) {
                                    ?><optgroup label="<?php esc_attr_e( $menu->name ); ?>"><?php
                                    $menuTree = $this->nav_menu_2_tree( $menu->slug );
                                    $this->printMenuTree( $menuTree, $menu->slug );
                                    ?></optgroup><?php
                                }
                                ?>
                            </select>
                        </label>
                    </div>

                </div>

                <div class="t42-panel t42-panel-box t42-date-time t42-margin <?php $this->is_hidden( 'date_time' ); ?>">

                    <h4><?php esc_html_e( 'Date & Time', 't42-content-protector' ); ?></h4>

                    <div class="t42-button-group" data-t42-button-radio="">
                        <button class="t42-button t42-button-primary t42-button-small t42-ignore t42-active"><?php esc_html_e( 'Ignore', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-success t42-button-small t42-include"><?php esc_html_e( 'Include', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-danger t42-button-small t42-exclude"><?php esc_html_e( 'Exclude', 't42-content-protector' ); ?></button>
                    </div>

                    <div class="t42-period-picker-box">
                        <p class="t42-period-picker t42-margin-top">
                            <!--suppress HtmlFormInputWithoutLabel -->
                            <input class="t42-period-picker-start" id="t42-period-picker-start" type="text" value=""/>
                            <!--suppress HtmlFormInputWithoutLabel -->
                            <input class="t42-period-picker-end" id="t42-period-picker-end" type="text" value=""/>
                        </p>

                        <p>
							<?php esc_html_e( 'The date and time assignments use the date/time of your servers, not that of the visitors system.', 't42-content-protector' ); ?>
                            <br>
							<?php esc_html_e( 'Current date/time:', 't42-content-protector' ); ?>
                            <strong><?php echo date( "d.m.Y H:i" ); ?></strong>
                        </p>
                    </div>

                </div>

                <div class="t42-panel t42-panel-box t42-user-roles t42-margin <?php $this->is_hidden( 'user_roles' ); ?>">

                    <h4><?php esc_html_e( 'User Roles', 't42-content-protector' ); ?></h4>

                    <div class="t42-button-group" data-t42-button-radio="">
                        <button class="t42-button t42-button-primary t42-button-small t42-ignore t42-active"><?php esc_html_e( 'Ignore', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-success t42-button-small t42-include"><?php esc_html_e( 'Include', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-danger t42-button-small t42-exclude"><?php esc_html_e( 'Exclude', 't42-content-protector' ); ?></button>
                    </div>

                    <div class="user-roles-box">
                        <p class="t42-margin-remove-bottom t42-margin-top"><?php esc_html_e( 'Select the user roles to assign to.', 't42-content-protector' ); ?></p>
                        <label>
                            <select class="user-roles t42-select2 t42-width-expand" data-placeholder="<?php esc_attr_e( 'Select some options', 't42-content-protector' ); ?>" multiple="">
                                <?php // Get user roles
                                $roles = get_editable_roles();
                                foreach ( $roles as $k => $role ) {
                                    ?><option value="<?php echo esc_attr( $k ); ?>"><?php esc_html_e( $role['name'] ); ?></option><?php
                                } ?>
                            </select>
                        </label>
                    </div>

                </div>

                <div class="t42-panel t42-panel-box t42-url t42-margin <?php $this->is_hidden( 'url' ); ?>">

                    <h4><?php esc_html_e( 'URL', 't42-content-protector' ); ?></h4>

                    <div class="t42-button-group" data-t42-button-radio="">
                        <button class="t42-button t42-button-primary t42-button-small t42-ignore t42-active"><?php esc_html_e( 'Ignore', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-success t42-button-small t42-include"><?php esc_html_e( 'Include', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-danger t42-button-small t42-exclude"><?php esc_html_e( 'Exclude', 't42-content-protector' ); ?></button>
                    </div>

                    <div class="t42-url-box">
                        <p class="t42-margin-top">
							<?php esc_html_e( 'Enter (part of) the URLs to match.', 't42-content-protector' ); ?><br>
							<?php esc_html_e( 'Use a new line for each different match.', 't42-content-protector' ); ?>
                        </p>

                        <!--suppress HtmlFormInputWithoutLabel -->
                        <textarea class="t42-url-field"></textarea>

                    </div>

                </div>

                <div class="t42-panel t42-panel-box t42-devices t42-margin <?php $this->is_hidden( 'devices' ); ?>">

                    <h4><?php esc_html_e( 'Devices', 't42-content-protector' ); ?></h4>

                    <div class="t42-button-group" data-t42-button-radio="">
                        <button class="t42-button t42-button-primary t42-button-small t42-ignore t42-active"><?php esc_html_e( 'Ignore', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-success t42-button-small t42-include"><?php esc_html_e( 'Include', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-danger t42-button-small t42-exclude"><?php esc_html_e( 'Exclude', 't42-content-protector' ); ?></button>
                    </div>

                    <div class="t42-devices-box">
                        <p class="t42-margin-remove-bottom t42-margin-top"><?php esc_html_e( 'Select the devices to assign to. Keep in mind that device detection is not always 100% accurate. Users can setup their device to mimic other devices.', 't42-content-protector' ); ?></p>
                        <label>
                            <select class="devices t42-select2 t42-width-expand" data-placeholder="<?php esc_attr_e( 'Select some options', 't42-content-protector' ); ?>" multiple="">
                                <option value="desktop"><?php esc_html_e( 'Desktop', 't42-content-protector' ); ?></option>
                                <option value="tablet"><?php esc_html_e( 'Tablet', 't42-content-protector' ); ?></option>
                                <option value="mobile"><?php esc_html_e( 'Mobile', 't42-content-protector' ); ?></option>
                            </select>
                        </label>
                    </div>

                </div>

                <div class="t42-panel t42-panel-box t42-php t42-margin <?php $this->is_hidden( 'custom_php' ); ?>">

                    <h4><?php esc_html_e( 'Custom PHP', 't42-content-protector' ); ?></h4>

                    <div class="t42-button-group" data-t42-button-radio="">
                        <button class="t42-button t42-button-primary t42-button-small t42-ignore t42-active"><?php esc_html_e( 'Ignore', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-success t42-button-small t42-include"><?php esc_html_e( 'Include', 't42-content-protector' ); ?></button>
                        <button class="t42-button t42-button-danger t42-button-small t42-exclude"><?php esc_html_e( 'Exclude', 't42-content-protector' ); ?></button>
                    </div>

                    <div class="t42-php-box">
                        <p class="t42-margin-top">
							<?php esc_html_e( 'Enter a piece of PHP code to evaluate. The code must return the value true or false.', 't42-content-protector' ); ?>
                            <br>
							<?php esc_html_e( 'For instance:', 't42-content-protector' ); ?>
                        </p>
                        <pre>$day_of_week = date('w');
if( '5' == $day_of_week ) { // Work only on Fridays.
    $result = true;
} else {
    $result = false;
}
return $result;</pre>

                        <!--suppress HtmlFormInputWithoutLabel -->
                        <textarea id="t42-php-field" name="t42-php-field" class="t42-php-field"></textarea>

                    </div>
                </div>
            </div>
        </div>

		<?php
	}

	/**
	 * Check if need to hide this assignments section and print class to hide it.
	 *
	 * @param string $section - Assignment section slug.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	private function is_hidden( $section ) {

		$hidden = 't42-hidden';

		if ( Plugin::get_tabs( "assignments:assignments:$section" ) ) {
			$hidden = '';
		}

		esc_attr_e( $hidden );

	}

	/**
	 * Checks if a plugin should work on current page.
	 *
	 * @return boolean
	 *
	 * @since 1.0.0
	 * @access protected
	 **/
	public function display() {

		/** Get plugin options. */
		$assignments_settings = Settings::get_instance()->options['assignments'];

		/** Get assignments for plugin. */
		$assignment = json_decode( str_replace( '|', '"', $assignments_settings ), false );

		/** If no settings - Show plugin Everywhere. */
		if ( ! $assignment ) { return true; }

		/** WordPress Content. */
		$wordPressContent = $this->WordPressContent( $assignment );

		/** Home Page. */
		$homePage = $this->HomePage( $assignment );

		/** Menu Items. */
		$menuItems = $this->MenuItems( $assignment );

		/** Date & Time */
		$dateTime = $this->DateTime( $assignment );

		/** User Roles. */
		$userRoles = $this->UserRoles( $assignment );

		/** URL. */
		$URL = $this->URL( $assignment );

		/** Devices. */
		$devices = $this->Devices( $assignment );

		/** Custom PHP. */
		$PHP = $this->PHP( $assignment );

		/** Matching Method. */
		return $this->MatchingMethod( $assignment, $wordPressContent, $homePage, $menuItems, $dateTime, $userRoles, $URL, $devices, $PHP );

	}

	/**
	 * Plugin Assignments - Date & Time.
	 *
	 * @param $assignment
	 *
	 * @return bool|int
	 * @since 1.0.0
	 * @access protected
	 */
	protected function DateTime( $assignment ) {

		/** If no dateTime - ignore. */
		if ( $assignment->dateTimeStart === '' || $assignment->dateTimeEnd === '' ) {
			return - 1;
		}

		$time = time();
		$s    = strtotime( $assignment->dateTimeStart ) - $time;
		$e    = strtotime( $assignment->dateTimeEnd ) - $time;

		switch ( $assignment->dateTime ) {
			case 1: // Include
				$result = false;
				if ( $s <= 0 && $e >= 0 ) {
					$result = true;
				}
				break;

			case 2: // Exclude
				$result = true;
				if ( $s <= 0 && $e >= 0 ) {
					$result = false;
				}
				break;

			default: // Ignore
				$result = - 1;
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - WordPress Content.
	 *
	 * @param $assignment
	 *
	 * @since 1.0.0
	 * @access protected
     *
	 * @return bool|int
	 **/
	protected function WordPressContent( $assignment ) {

		$result = - 1;

		switch ( $assignment->WPContent ) {

			case 0: // Ignore
				$result = - 1;
				break;

			case 1: // Include
				$result = $this->check_wordpress_content( false, $assignment );
				break;

			case 2: // Exclude
				$result = $this->check_wordpress_content( true, $assignment );
				break;

		}

		return $result;

	}

	/**
     * Check assignments for WordPress Content.
     *
	 * @param $flag
	 * @param $assignment
	 *
	 * @return bool|int
	 **/
	private function check_wordpress_content( $flag, $assignment ) {

		/** If no values - ignore. */
		$result = $flag;
		if ( ! $assignment->WPContentVal ) {
			return - 1;
		}

		$query = $this->getQuery();
		foreach ( $query as $q ) {

			if ( in_array( $q, $assignment->WPContentVal, true ) ) {
				return ! $flag;
			}
		}

		return $result;

	}

	/**
	 * Plugin assignments - Home Page.
	 *
	 * @param $assignment
	 *
	 * @return bool|int
	 * @since 1.0.0
	 * @access protected
	 */
	protected function HomePage( $assignment ) {
		switch ( $assignment->homePage ) {

			case 1: // Include
				$result = false;
				if ( is_front_page() ) {
					$result = true;
				}
				break;

			case 2: // Exclude
				$result = true;
				if ( is_front_page() ) {
					$result = false;
				}
				break;

			default: // Ignore
                $result = - 1;
                break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - Menu Items.
	 *
	 * @param $assignment
	 *
	 * @since 1.0.0
	 * @access protected
     *
	 * @return bool|int
	 **/
	protected function MenuItems( $assignment ) {

		$result = - 1;

		// If wrong input array - Ignore
		if ( ! is_array( $assignment->menuItemsVal ) ) {
			return - 1;
		}

		// Current URL
		if ( ! isset( $_SERVER["HTTPS"] ) || ( $_SERVER["HTTPS"] !== 'on' ) ) {

			/** @noinspection HttpUrlsUsage */
			$currentUrl = 'http://' . $_SERVER["SERVER_NAME"];

		} else {

			$currentUrl = 'https://' . $_SERVER["SERVER_NAME"];

		}
		$currentUrl .= $_SERVER["REQUEST_URI"];

		switch ( $assignment->menuItems ) {
			case 0: // Ignore
				$result = - 1;
				break;

			case 1: // Include
				$result = false;
				if ( ! $assignment->menuItemsVal ) {
					return - 1;
				} // If no menu items - ignore

				$menu_items_arr = []; // Assignments menu items
				foreach ( $assignment->menuItemsVal as $val ) {
					if ( $val === '' ) {
						continue;
					}
					list( $menuSlug, $menuItemID ) = explode( "+", $val );
					$menu_items       = wp_get_nav_menu_items( $menuSlug );
					$menu_item        = wp_filter_object_list( $menu_items, [ 'ID' => $menuItemID ] );
					$menu_items_arr[] = reset( $menu_item );
				}

				foreach ( $menu_items_arr as $mItem ) {
					if ( $currentUrl === $mItem->url ) {
						return true;
					}
				}
				break;

			case 2: // Exclude
				$result = true;
				if ( ! $assignment->menuItemsVal ) {
					return - 1;
				} // If no menu items - ignore

				$menu_items_arr = []; // Assignments menu items

				foreach ( $assignment->menuItemsVal as $val ) {
					list( $menuSlug, $menuItemID ) = explode( "+", $val );
					$menu_items       = wp_get_nav_menu_items( $menuSlug );
					$menu_item        = wp_filter_object_list( $menu_items, [ 'ID' => $menuItemID ] );
					$menu_items_arr[] = reset( $menu_item );
				}

				foreach ( $menu_items_arr as $mItem ) {
					if ( $currentUrl === $mItem->url ) {
						return false;
					}
				}
				break;
		}

		return $result;

	}

	/**
	 * Plugin assignments - User Roles.
	 *
	 * @param $assignment
	 *
	 * @return bool|int
	 * @since 1.0.0
	 * @access protected
	 */
	protected function UserRoles( $assignment ) {

		// If wrong input array - Ignore
		if ( ! is_array( $assignment->userRolesVal ) ) {
			return - 1;
		}

		include_once( ABSPATH . 'wp-includes/pluggable.php' );

		switch ( $assignment->userRoles ) {

			case 1: // Include
				$result = false;
				$user   = wp_get_current_user();
				if ( null === $user ) { break; }

				foreach ( $user->roles as $role ) {
					if ( in_array( $role, $assignment->userRolesVal, false ) ) {
						$result = true;
					}
				}
				break;

			case 2: // Exclude
				$result = true;
				$user   = wp_get_current_user();
				if ( null === $user ) { break; }

				foreach ( $user->roles as $role ) {
					if ( in_array( $role, $assignment->userRolesVal, false ) ) {
						$result = false;
					}
				}
				break;

			default: // Ignore
				$result = - 1;
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - Devices.
	 *
	 * @param $assignment
	 *
	 * @return bool|int
	 * @since 1.0.0
	 * @access protected
	 */
	protected function Devices( $assignment ) {

		$detect = new MobileDetect;

		/** Detect current user device. */
		$device = 'desktop';
		if ( $detect->isTablet() ) {
			$device = 'tablet';
		}
		if ( $detect->isMobile() && ! $detect->isTablet() ) {
			$device = 'mobile';
		}

		/** If wrong input array - Ignore. */
		if ( ! is_array( $assignment->devicesVal ) ) {

			return - 1;

		}

		switch ( $assignment->devices ) {

			case 1: // Include
				$result = false;
				if ( in_array( $device, $assignment->devicesVal,false ) ) {
					$result = true;
				}
				break;

			case 2: // Exclude
				$result = true;
				if ( in_array( $device, $assignment->devicesVal, false ) ) {
					$result = false;
				}
				break;

			default: // Ignore
				$result = - 1;
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - URL.
	 *
	 * @param $assignment
	 *
	 * @return bool|int
	 * @since 1.0.0
	 * @access protected
	 */
	protected function URL( $assignment ) {

		/** Current URL. */
		if ( ! isset( $_SERVER["HTTPS"] ) || ( $_SERVER["HTTPS"] !== 'on' ) ) {

			/** @noinspection HttpUrlsUsage */
			$current_url = 'http://' . $_SERVER["SERVER_NAME"];

		} else {

			$current_url = 'https://' . $_SERVER["SERVER_NAME"];

		}
		$current_url .= $_SERVER["REQUEST_URI"];

		$URLVal = (array) preg_split( '/\r\n|[\r\n]/', $assignment->URLVal );
		$URLVal = array_filter( $URLVal, static function ( $value ) {
			if ( trim( $value ) !== '' ) {
				return $value;
			}

			return null;
		} );

		switch ( $assignment->URL ) {

			case 1: // Include
				$result = false;
				if ( count( $URLVal ) === 0 ) {
					$result = false;
				} // If no URLS to include - hide widget
				foreach ( $URLVal as $u ) {
					if ( strpos( $current_url, $u ) !== false ) {
						$result = true;
					}
				}

				break;

			case 2: // Exclude
				$result = true;
				if ( count( $URLVal ) === 0 ) {
					$result = true;
				} // If no URLS to exclude - show widget
				foreach ( $URLVal as $u ) {
					if ( strpos( $current_url, $u ) !== false ) {
						$result = false;
					}
				}
				break;

			default: // Ignore
				$result = - 1;
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - Custom PHP.
	 *
	 * @param $assignment
	 *
	 * @return bool|int
	 * @since 1.0.0
	 * @access protected
	 */
	protected function PHP( $assignment ) {

		/** Replace <?php and other fix stuff. */
		$php = trim( $assignment->PHPVal );

		$p = substr( $php, 0, 5 );
		if ( strtolower( $p ) === '<?php' ) {
			$php = substr( $php, 5 );
		}

		$php = trim( $php );

		/** Ignore settings if empty php code. */
		if ( $php === '' ) {
			return - 1;
		}

		$php .= ';return true;';

		/** Evaluate the script. */
		ob_start();
		$pass = (bool) eval( $php );
		ob_end_clean();

		switch ( $assignment->PHP ) {

			case 1: // Include
				$result = false;
				if ( $pass ) {
					$result = true;
				}

				break;

			case 2: // Exclude
				$result = true;
				if ( $pass ) {
					$result = false;
				}
				break;

			default: // Ignore
				$result = - 1;
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - Matching Method.
	 *
	 * @param $assignment
	 * @param $wordPressContent
	 * @param $homePage
	 * @param $menuItems
	 * @param $dateTime
	 * @param $userRoles
	 * @param $URL
	 * @param $devices
	 * @param $PHP
	 *
	 * @return bool
	 * @since 1.0.0
	 * @access protected
	 */
	protected function MatchingMethod( $assignment, $wordPressContent, $homePage, $menuItems, $dateTime, $userRoles, $URL, $devices, $PHP ) {

		$arrCond = []; // Add condition values

		// Ignore if -1
		if (  - 1 !== $wordPressContent ) {
			$arrCond[] = $wordPressContent;
		}

		if ( $homePage !== - 1 ) {
			$arrCond[] = $homePage;
		}

		if ( $menuItems !== - 1 ) {
			$arrCond[] = $menuItems;
		}

		if ( $dateTime !== - 1 ) {
			$arrCond[] = $dateTime;
		}

		if ( $userRoles !== - 1 ) {
			$arrCond[] = $userRoles;
		}

		if ( $URL !== - 1 ) {
			$arrCond[] = $URL;
		}

		if ( $devices !== - 1 ) {
			$arrCond[] = $devices;
		}

		if ( $PHP !== - 1 ) {
			$arrCond[] = $PHP;
		}

		if ( ! count( $arrCond ) ) {
			$arrCond[] = true;
		}

		// If all rules are Ignore - Show widget
		// Initialization
		$any_true = false;
		$all_true  = true;

		// Processing
		foreach ( $arrCond as $v ) {
			$any_true |= $v;
			$all_true  &= $v;
		}

		// Result
		if ( $all_true ) {
			// All elements are TRUE
			$result = true;
		} elseif ( ! $any_true ) {
			// All elements are FALSE
			$result = false;
		} else if ( $assignment->matchingMethod === 0 ) { // ALL RULES
            $result = false;
        } else { // ANY OF RULES
            $result = true;
        }

		return $result;
	}

	/**
	 * Output options list for select.
	 *
	 * @param $arr
	 * @param $menu_slug
	 * @param int $level
	 *
	 * @since 1.0.0
	 */
	public function printMenuTree( $arr, $menu_slug, $level = 0 ) {
		foreach ( $arr as $item ) {
			?>
            <option value="<?php echo esc_attr( $menu_slug . "+" . $item->ID ); ?>">
                <?php echo str_repeat( "-", $level ) . " " . $item->title . " (" . $item->type_label . ")"; ?>
            </option>
            <?php
			if (  is_countable( $item->wpse_children ) && count( $item->wpse_children ) ) {
				$this->printMenuTree( $item->wpse_children, $menu_slug, $level + 1 );
			}
		}
	}

	/**
	 * Transform a navigational menu to it's tree structure
	 *
	 * @param $menu_id
	 *
	 * @return array|null $tree
	 * @uses  buildTree()
	 * @uses  wp_get_nav_menu_items()
	 *
	 **/
	public function nav_menu_2_tree( $menu_id ) {

		$items = wp_get_nav_menu_items( $menu_id );

		return  $items ? $this->buildTree( $items ) : null;

	}

	/**
	 * Modification of "Build a tree from a flat array in PHP"
	 *
	 * @link https://stackoverflow.com/a/28429487/2078474
	 *
	 * @param array $elements
	 * @param int   $parentId
	 *
	 * @return array
	 **/
	public function buildTree( array &$elements, $parentId = 0 ) {

		$branch = [];

		foreach ( $elements as &$element ) {

			if ( $element->menu_item_parent === $parentId ) {

				$children = $this->buildTree( $elements, $element->ID );

				if ( $children ) {
					$element->wpse_children = $children;
				}

				$branch[$element->ID] = $element;
				unset( $element );

			}

		}

		return $branch;

	}

	/**
	 * Get current query information.
	 *
	 *
	 * @global WP_Query $wp_query
	 *
     * @return string[]
	 **/
	public function getQuery() {
		global $wp_query;

		// create, if not set
		if ( empty( $this->query ) ) {

			// init vars
			$obj   = $wp_query->get_queried_object();

			$type  = get_post_type();
			$query = [];

			if ( is_home() ) {
				$query[] = 'home';
			}

			if ( is_front_page() ) {
				$query[] = 'front_page';
			}

			if ( $type === 'post' ) {

				if ( is_single() ) {
					$query[] = 'single';
				}

				if ( is_archive() ) {
					$query[] = 'archive';
				}
			} else if ( is_single() ) {

                $query[] = $type . '-single';

            } elseif ( is_archive() ) {

                $query[] = $type . '-archive';

            }

			if ( is_search() ) {
				$query[] = 'search';
			}

			if ( null !== $obj && is_page() ) {
				$query[] = $type;
				$query[] = $type . '-' . $obj->ID;
			}

			if ( null !== $obj && is_category() ) {
				$query[] = 'cat-' . $obj->term_id;
			}

			/** WooCommerce. */
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

				/** @noinspection PhpUndefinedFunctionInspection */
				if ( is_shop() && ! is_search() ) {
					$query[] = 'page';

					/** @noinspection PhpUndefinedFunctionInspection */
					$query[] = 'page-' . wc_get_page_id( 'shop' );
				}

				/** @noinspection PhpUndefinedFunctionInspection */
				if ( is_product_category() || is_product_tag() ) {
					$query[] = 'cat-' . $obj->term_id;
				}
			}

			$this->query = $query;

		}

		return $this->query;
	}

	/**
	 * Main TabAssignments Instance.
	 *
	 * Insures that only one instance of TabAssignments exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
     *
	 * @return TabAssignments
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

} // End Class TabAssignments.