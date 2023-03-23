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

/**
 * Merlin WP
 * Better WordPress Theme Onboarding
 *
 * The following code is a derivative work from the
 * Envato WordPress Theme Setup Wizard by David Baker.
 *
 * @package   Merlin WP
 * @version   1.0.0
 * @link      https://merlinwp.com/
 * @author    Rich Tabor, from ThemeBeans.com & the team at ProteusThemes.com
 * @copyright Copyright (c) 2018, Merlin WP of Inventionn LLC
 * @license   Licensed GPLv3 for Open Source Use
 */

namespace T42\ContentProtector;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Merlin wizard.
 *
 * Based on Merlin WP by Rich Tabor
 * @see https://merlinwp.com/
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
class Merlin {

	/**
	 * Current step.
	 *
	 * @since 1.0.0
     *
	 * @var string
	 **/
	protected $step = '';

	/**
	 * Steps.
     *
	 * @since 1.0.0
	 *
	 * @var array
	 **/
	protected $steps = [];

	/**
	 * Top level admin page.
	 *
	 * @var string $merlin_url
	 */
	protected $merlin_url;

	/**
	 * The capability required for this menu to be displayed to the user.
	 *
	 * @var string $capability
	 */
	protected $capability;

	/**
	 * Class Constructor.
	 *
     * @since 1.0.0
     * @access public
     *
     * @return void
	 **/
	public function __construct() {

		/** Set config arguments. */
		$this->merlin_url             = 't42-content-protector-wizard'; // The wp-admin page slug where Merlin WP loads.
		$this->capability             = 'manage_options';

		add_action( 'admin_init', [ $this, 'redirect' ], 30 );
		add_action( 'admin_init', [ $this, 'steps' ], 30, 0 );
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_page' ], 30, 0 );
		add_action( 'admin_footer', [ $this, 'svg_sprite' ] );

		add_action( 'wp_ajax_merlin_subscribe', [ $this, '_ajax_merlin_subscribe' ], 10, 0 );
		add_action( 'wp_ajax_merlin_activate', [ $this, '_ajax_merlin_activate' ], 10, 0 );

	}

	/**
	 * Ajax Activate plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function _ajax_merlin_activate() {

		/** Check nonce for security. */
		check_ajax_referer( 'merlin_nonce', 'wpnonce' );

		/** Prepare variables. */
		$purchase_code = filter_input( INPUT_POST, 'purchase_code', FILTER_SANITIZE_STRING );
		$is_activated = PluginActivation::get_instance()->is_activated( $purchase_code );

		/** Write purchase code to options. */
		$key = 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id();
		update_option( 't42_content_protector_activation_settings', [$key => $purchase_code] );

        /**
         * The Purchase Code entered by the user must be stored in the wp_options table as a string
         * using the following syntax:
         *    option_name: envato_purchase_code_<item_id>
         *    option_value: <purchase_code>
         **/
		update_option( $key, $purchase_code );

		/** Return activation result. */
		wp_send_json( [
			'success' => $is_activated,
		] );

    }

	/**
	 * Ajax Subscribe user.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
	 **/
	public function _ajax_merlin_subscribe() {

		/** Check nonce for security. */
		check_ajax_referer( 'merlin_nonce', 'wpnonce' );

		/** Prepare variables. */
		$mail = filter_var( $_POST[ 'mail' ], FILTER_VALIDATE_EMAIL );
		$join = filter_input( INPUT_POST, 'join', FILTER_SANITIZE_STRING );
		$slug = Plugin::get_slug();

		/** Do we have valid email? */
		if ( ! $mail ) {

			wp_send_json( [
                'success' => false,
                'message' => esc_html__( 'Wrong E-mail value.', 't42-content-protector' ),
            ] );

		}

		/** Build request url. */
		$url = 'https://updates.42theme.com/wp-json/t42-purchase-validator/v1/subscribe?';
		$url .= 'mail=' . $mail;
		$url .= '&join=' . $join;
		$url .= '&slug=' . $slug;

		/** Send subscribe request. */
		$subscribe_result = Helper::get_instance()->get_remote( $url );

		/** Return FALSE on error. */
		if ( false === $subscribe_result ) {

			wp_send_json( [
				'success' => false,
				'message' => esc_html__( 'Subscription failed.', 't42-content-protector' ),
			] );

		}

		/** Return Subscribe result. */
		wp_send_json( [
			'success' => true,
			'message' => $subscribe_result,
		] );

	}

	/**
	 * Redirection transient.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
	 **/
	public function redirect() {

	    /** @see https://stackoverflow.com/questions/7381661/cannot-modify-header-information-headers-already-sent-by-wordpress-issue */
		ob_start();

	    $transient_key = Plugin::get_slug() . '_merlin_redirect';

		if ( ! get_transient( $transient_key ) ) {
			return;
		}

		delete_transient( $transient_key );

		wp_safe_redirect( menu_page_url( $this->merlin_url ) );

		exit;

	}

	/**
	 * Add the admin menu item, under Appearance.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
	 **/
	public function add_admin_menu() {

		add_submenu_page(
			'',
            esc_html__( 'Plugin Setup', 't42-content-protector' ),
            esc_html__( 'Plugin Setup', 't42-content-protector' ),
            sanitize_key( $this->capability ),
            sanitize_key( $this->merlin_url ),
            [ $this, 'admin_page' ]
		);

	}

	/**
	 * Add the admin page.
     *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function admin_page() {

		/** Do not proceed, if we're not on the right page. */
		if ( empty( $_GET['page'] ) || $this->merlin_url !== $_GET['page'] ) { return; }

		if ( ob_get_length() ) {
			ob_end_clean();
		}

		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		/** Enqueue MP merlin.css */
		AdminStyles::get_instance()->enqueue_style( 'merlin', true, [ 'wp-admin' ] );

		/** Enqueue MP merlin.js */
		AdminScripts::get_instance()->enqueue_script( 'merlin', true, ['jquery-core'] );

        wp_localize_script(
            't42-content-protector-mp-merlin', 'merlin_params', [
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'wpnonce' => wp_create_nonce( 'merlin_nonce' ),
            ]
        );

		ob_start();

		/** Start the actual page content. */
		$this->header(); ?>

		<div class="merlin__wrapper">

			<div class="merlin__content merlin__content--<?php esc_attr_e( strtolower( $this->steps[ $this->step ]['name'] ) ); ?>">

				<?php
				/** Content Handlers. */
				$show_content = true;

				if ( ! empty( $_REQUEST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
					$show_content = call_user_func( $this->steps[ $this->step ]['handler'] );
				}

				if ( $show_content ) {
					$this->body();
				}
				?>

			    <?php $this->step_output(); ?>

			</div>

			<a class="return-to-dashboard" href="<?php echo esc_url( admin_url( '/' ) ); ?>">
                <?php esc_html_e( 'Return to the WordPress Dashboard', 't42-content-protector' ); ?>
            </a>

            <a class="go-to-plugin" href="<?php echo esc_url( admin_url( '/admin.php?page=t42_content_protector_settings' ) ); ?>">
	            <?php esc_html_e( 'Go to Plugin Settings', 't42-content-protector' ); ?>
            </a>

		</div>

		<?php $this->footer(); ?>

		<?php
		exit;
	}

	/**
	 * Output the header.
     *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
	protected function header() {

		// Get the current step.
		$current_step = strtolower( $this->steps[ $this->step ]['name'] );
		?>

		<!DOCTYPE html>
		<!--suppress HtmlRequiredLangAttribute -->
        <html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
		<!--suppress HtmlRequiredTitleElement -->
        <head>
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<?php printf( esc_html__( '%1$s%2$s Plugins &rsaquo; Plugin Setup: %3$s%4$s', 't42-content-protector' ), '<ti', 'tle>', esc_html( Plugin::get_name() ), '</title>' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="merlin__body merlin__body--<?php esc_attr_e( $current_step ); ?>">
		<?php
	}

	/**
	 * Output the content for the current step.
     *
     * @since 1.0.0
     * @access protected
     *
     * @return void
	 **/
	protected function body() {

	    if ( isset( $this->steps[ $this->step ] ) ) {

		    call_user_func( $this->steps[ $this->step ]['view'] );

        }

	}

	/**
	 * Output the footer.
     *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
	protected function footer() {

		do_action( 'admin_footer' );
		do_action( 'admin_print_footer_scripts' );
		?>
		</body>
		</html>
		<?php

	}

	/**
	 * SVG.
     *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function svg_sprite() {

		/** Define SVG sprite file. */
		$svg = Plugin::get_path() . 'images/sprite.svg';

		/** Exit if file not exists. */
		if ( ! file_exists( $svg ) ) { return; }

        /** @noinspection PhpIncludeInspection */
        require_once apply_filters( 'merlin_svg_sprite', $svg );

	}

	/**
	 * Return SVG markup.
	 *
	 * @since 1.0.0
	 * @access public
     *
	 * @param array $args {
	 *     Parameters needed to display an SVG.
	 *
	 *     @type string $icon  Required SVG icon filename.
	 *     @type string $title Optional SVG title.
	 *     @type string $desc  Optional SVG description.
	 * }
     *
	 * @return string SVG markup.
	 **/
	public function svg( $args = [] ) {

		/** Make sure $args are an array. */
		if ( empty( $args ) ) {
			return esc_html__( 'Please define default parameters in the form of an array.', 't42-content-protector' );
		}

		// Define an icon.
		if ( false === array_key_exists( 'icon', $args ) ) {
			return __( 'Please define an SVG icon filename.', 't42-content-protector' );
		}

		// Set defaults.
		$defaults = [
			'icon'        => '',
			'title'       => '',
			'desc'        => '',
		];

		// Parse args.
		/** @noinspection CallableParameterUseCaseInTypeContextInspection */
		$args = wp_parse_args( $args, $defaults );

        $aria_hidden = ' aria-hidden="true"';

		// Set ARIA.
		$aria_labelledby = '';

		if ( $args['title'] && $args['desc'] ) {
			$aria_labelledby = ' aria-labelledby="title desc"';
		}

		// Begin SVG markup.
		$svg = '<svg class="icon icon--' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';

		// If there is a title, display it.
		if ( $args['title'] ) {
			$svg .= '<title>' . esc_html( $args['title'] ) . '</title>';
		}

		// If there is a description, display it.
		if ( $args['desc'] ) {
			$svg .= '<desc>' . esc_html( $args['desc'] ) . '</desc>';
		}

		$svg .= '<use xlink:href="#icon-' . esc_html( $args['icon'] ) . '"></use>';

		$svg .= '</svg>';

		return $svg;
	}

	/**
	 * Allowed HTML for sprites.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 **/
	public function svg_allowed_html() {

		$array = [
			'svg' => [
				'class'       => [],
				'aria-hidden' => [],
				'role'        => [],
			],
			'use' => [
				'xlink:href' => [],
			],
		];

		return apply_filters( 'merlin_svg_allowed_html', $array );

	}

	/**
	 * Setup steps.
     *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function steps() {

		$this->steps = [
			'welcome' => [
				'name'    => esc_html__( 'Welcome', 't42-content-protector' ),
				'view'    => [ $this, 'welcome' ],
			],
		];

		$this->steps['requirements'] = [
			'name' => esc_html__( 'System Requirements', 't42-content-protector' ),
			'view' => [ $this, 'requirements' ],
		];

        $this->steps['security'] = [
	        'name' => esc_html__( 'Security Alerts', 't42-content-protector' ),
	        'view' => [ $this, 'security' ],
        ];

        $this->steps['license'] = [
            'name' => esc_html__( 'License', 't42-content-protector' ),
            'view' => [ $this, 'license' ],
        ];

		$this->steps['ready'] = [
			'name' => esc_html__( 'Ready', 't42-content-protector' ),
			'view' => [ $this, 'ready' ],
		];

		/** To add custom steps. */
		$this->steps = apply_filters( Plugin::get_slug() . '_merlin_steps', $this->steps );

	}

	/**
	 * Output the steps.
     *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
	protected function step_output() {

		$output_steps  = $this->steps;
		$array_keys   = array_keys( $this->steps );
		$current_step = array_search( $this->step, $array_keys, true );

		array_shift( $output_steps );
		?>

		<ol class="dots">

			<?php
			foreach ( $output_steps as $step_key => $step ) :

				$class_attr = '';

				if ( $step_key === $this->step ) {
					$class_attr = 'active';
				} elseif ( $current_step > array_search( $step_key, $array_keys, true ) ) {
					$class_attr = 'done';
				}
				?>

				<li class="<?php esc_attr_e( $class_attr ); ?>">
					<a href="<?php echo esc_url( $this->step_link( $step_key ) ); ?>" title="<?php esc_attr_e( $step['name'] ); ?>"></a>
				</li>

			<?php endforeach; ?>

		</ol>

		<?php
	}

	/**
	 * Get the step URL.
	 *
     * @since 1.0.0
	 * @access protected
	 *
	 * @param string $step Name of the step, appended to the URL.
	 *
	 * @return string
	 **/
	protected function step_link( $step ) {

		return add_query_arg( 'step', $step );

	}

	/**
	 * Get the next step link.
     *
	 * @since 1.0.0
	 * @access protected
     *
     * @return string
	 **/
	protected function step_next_link() {

		$keys = array_keys( $this->steps );
		$step = array_search( $this->step, $keys, true ) + 1;

		return add_query_arg( 'step', $keys[ $step ] );

	}

	/**
	 * Welcome step.
     *
     * @since 1.0.0
	 * @access protected
	 *
     * @return void
	 **/
	protected function welcome() {

		/** Has this plugin been setup yet? Compare this to the option set when you get to the last panel. */
		$already_setup = get_option( 't42-content-protector_merlin_completed' );

		/** Text strings. */
		$header    = ! $already_setup ? esc_html__( 'Welcome to %s', 't42-content-protector' ) : esc_html__( 'Hi. Welcome back', 't42-content-protector' );
		$paragraph = ! $already_setup ? esc_html__( 'This wizard will help you configure your new plugin. It is optional & should take only a few minutes.', 't42-content-protector' ) : esc_html__( 'You may have already run this plugin setup wizard. If you would like to proceed anyway, click on the "Start" button below.', 't42-content-protector' );

		?>

		<div class="merlin__content--transition">
			<?php $this->render_icon( 'welcome' ); ?>

			<h1><?php esc_html_e( sprintf( $header, Plugin::get_name() ) ); ?></h1>
			<p><?php esc_html_e( sprintf( $paragraph, Plugin::get_name() ) ); ?></p>
            <br>
            <p><?php esc_html_e( 'No time right now? You can cancel and return to the WordPress dashboard.', 't42-content-protector' ); ?></p>
		</div>

		<footer class="merlin__content__footer">
			<a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '/' ) ); ?>" class="merlin__button merlin__button--skip">
                <?php esc_html_e( 'Cancel', 't42-content-protector' ); ?>
            </a>
			<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next merlin__button--proceed">
                <?php esc_html_e( 'Start', 't42-content-protector' ); ?>
            </a>
			<?php wp_nonce_field( 'merlin' ); ?>
		</footer>

	    <?php
	}

	/**
	 * License step.
     *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
	protected function license() {
		?>
		<div class="merlin__content--transition">

			<?php
			$this->render_icon( 'license' );
			$this->render_checkmark_icon();
			?>

			<h1>
                <?php esc_html_e( 'Activate ', 't42-content-protector' ) . Plugin::get_name(); ?>
            </h1>

			<p id="license-text">
                <?php esc_html_e( 'Enter your CodeCanyon Purchase Code to enable remote updates and plugin support.', 't42-content-protector' ); ?>
            </p>

            <div class="merlin__content--license-key">
                <label for="license-key"><?php esc_html_e( 'Purchase Code', 't42-content-protector' ); ?></label>

                <div class="merlin__content--license-key-wrapper">
                    <?php
                    /** Prepare variables. */
                    $key = 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id();
                    $purchase_code = get_option( $key, '' );
                    ?>
                    <input type="text"
                           value="<?php esc_attr_e( $purchase_code ); ?>"
                           id="license-key"
                           class="js-license-key"
                           placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX"
                           autocomplete="off"
                           autocorrect="off"
                           autocapitalize="off"
                           spellcheck="false"
                    >
                    <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"
                       title="<?php esc_html_e( 'Where is my Purchase Code?', 't42-content-protector' ); ?>"
                       target="_blank"
                    >
                        <span class="hint--top" aria-label="<?php esc_html_e( 'Where is my Purchase Code?', 't42-content-protector' ); ?>">
                            <?php $this->render_icon( 'help' ); ?>
                        </span>
                    </a>

                </div>
                <div class="t42-merlin-input-desc">
                    <?php esc_html_e( 'If you downloaded this plugin from Envato Elements you can skip this step.', 't42-content-protector' ); ?>
                </div>
            </div>

		</div>

		<footer class="merlin__content__footer">
            <a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed">
                <?php esc_html_e( 'Skip', 't42-content-protector' ); ?>
            </a>
            <a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next button-next t42-merlin-license-activate-btn">
                <span class="merlin__button--loading__text"><?php esc_html_e( 'Activate', 't42-content-protector' ); ?></span>
            </a>
			<?php wp_nonce_field( 'merlin' ); ?>
		</footer>
		<?php
	}

	/**
	 * System Requirements Step.
     *
     * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
	protected function requirements() {

		/** Variables. */
		$is_passed = true;

		?>

		<div class="merlin__content--transition">

			<?php
			$this->render_icon( 'requirements' );
			$this->render_checkmark_icon();
            ?>

			<h1><?php esc_html_e( 'Checking Server Environment', 't42-content-protector' ); ?></h1>

			<p id="requirements-text">
                <?php esc_html_e( 'Please make sure that your server meets the minimum requirements to avoid a low performance and 
                troubles using this plugin.', 't42-content-protector' ); ?>
            </p>

		</div>

        <ul class="merlin__drawer merlin__drawer--import-content js-merlin-drawer-import-content">
            <?php

            /** Get enabled requirements from server reporter. */
            $requirements = Plugin::get_tabs( 'status:reports:server' );
            unset( $requirements[ 'enabled' ], $requirements[ 'os' ], $requirements[ 'software' ], $requirements[ 'mysql_version' ] );
            $requirements = array_filter( $requirements );

            /** Prepare Requirements Report. */
            $report = ReporterServer::get_instance()->get_report();

            foreach ( $requirements as $key => $v ) :

                $row = $report[$key];

                /** Prepare css class. */
                $class = 'success';
                $message = '';
                if ( isset( $row['warning'] ) && $row['warning'] ) {
                    $class = 'error';
	                $message = isset( $row['recommendation'] ) ? $row['recommendation'] : $row['value'];
	                $is_passed = false;
                }
            ?>
                <li class="merlin__drawer--import-content__list-item status status--Pending">
                    <input type="checkbox"
                           class="checkbox checkbox-<?php esc_attr_e( $key ); ?>"
                           id="requirements_<?php esc_attr_e( $key ); ?>"
                           value="1"
                           checked=""
                           disabled=""
                    >
                    <label for="requirements_<?php esc_attr_e( $key ); ?>"
                           class="<?php esc_attr_e( $class ); ?>"
                           data-class="<?php esc_attr_e( $class ); ?>"
                    >
                        <i></i><span><?php esc_html_e( $row['label'] ); ?></span>
                    </label>
                    <?php if ( $message ) : ?>
                        <div class="merlin_requirements_warning"><?php echo wp_kses_post( $message ); ?></div>
                    <?php endif; ?>
                </li>
            <?php endforeach;?>
        </ul>

		<footer class="merlin__content__footer">

			<?php if ( ! $is_passed ) : ?>

				<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed">
                    <?php esc_html_e( 'Skip', 't42-content-protector' ); ?>
                </a>

			<?php else : ?>

				<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next merlin__button--proceed">
                    <?php esc_html_e( 'Next', 't42-content-protector' ); ?>
                </a>

			<?php endif; ?>

			<?php wp_nonce_field( 'merlin' ); ?>
		</footer>
	<?php
	}

	/**
	 * Security Alerts step.
     *
     * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
	protected function security() {
		?>
		<div class="merlin__content--transition">

			<?php
            $this->render_icon( 'content' );
			$this->render_checkmark_icon();
            ?>

			<h1><?php esc_html_e( 'Security Alerts', 't42-content-protector' ); ?></h1>

			<p><?php esc_html_e( 'Please tell us where Content Protector should send you security alerts for your website.', 't42-content-protector' ); ?></p>

            <div class="t42-merlin-subscribe-box">
                <?php
                /** @noinspection PhpUnusedLocalVariableInspection */
                list( $name, $mail ) = Helper::get_instance()->get_current_user_details();

                /** Email field. */
                UI::get_instance()->render_text_input(
                        $mail,
                        null,
                        '',
                        [
                            'class'         => 't42-merlin-email',
                            'name'          => 't42-merlin-email-input',
                            'placeholder'   => 'name@example.com'
                        ]
                );
                ?>
                <div class="t42-merlin-input-desc"><?php esc_html_e( 'We don\'t use your email for any other purpose. You may unsubscribe anytime.', 't42-content-protector' ); ?></div>

                <ul class="merlin__drawer--import-content">
                    <li class="merlin__drawer--import-content__list-item status status--Pending">
                        <input type="checkbox" name="t42-merlin-subscribe" class="checkbox" id="t42-merlin-subscribe" value="1">
                        <label for="t42-merlin-subscribe">
                            <i></i><span><?php esc_html_e( 'Would you also like to join our mailing list?', 't42-content-protector' ); ?></span>
                        </label>
                    </li>
                </ul>
            </div>

		</div>

		<form action="" method="post">
            <footer class="merlin__content__footer">
                <a id="skip" href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed">
					<?php esc_html_e( 'Skip', 't42-content-protector' ); ?>
                </a>
                <a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next button-next t42-merlin-subscribe-btn">
                    <span class="merlin__button--loading__text"><?php esc_html_e( 'Continue', 't42-content-protector' ); ?></span>
                </a>
				<?php wp_nonce_field( 'merlin' ); ?>
            </footer>
		</form>

	<?php
	}

	/**
	 * Final step.
	 *
     * @since 1.0.0
     * @access protected
     *
     * @return void
     *
	 * @noinspection HtmlUnknownTarget
	 **/
	protected function ready() {

		/** Prepare author link. */
		$author = '<a href="https://42theme.com/" target="_blank" rel="noopener">42Theme</a>';

		/** Links. */
		$links = [];
		$links[] = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://1.envato.market/42themeCC', esc_html__( 'Explore Our Plugins', 't42-content-protector' ) );
		$links[] = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://bit.ly/3uE2tD6', esc_html__( 'Our Hosting Provider', 't42-content-protector' ) );

		/** Mark wizard as completed. */
		update_option( 't42-content-protector_merlin_completed', time() );
		?>

		<div class="merlin__content--transition">

			<?php $this->render_icon( 'done' ); ?>

			<h1><?php esc_html_e( 'All done. Have fun!', 't42-content-protector' ); ?></h1>

			<p><?php printf( esc_html__( 'Enjoy your new plugin by %s', 't42-content-protector' ), $author ); ?></p>

		</div>

		<footer class="merlin__content__footer merlin__content__footer--fullwidth">

			<a href="<?php echo get_home_url(); ?>" class="merlin__button merlin__button--blue merlin__button--fullwidth merlin__button--popin">
                <?php esc_html_e( 'View your website', 't42-content-protector' ); ?>
            </a>

			<?php if ( ! empty( $links ) ) : ?>
				<a id="merlin__drawer-trigger" class="merlin__button merlin__button--knockout">
                    <span><?php esc_html_e( 'Extras', 't42-content-protector' ); ?></span>
                    <span class="chevron"></span>
                </a>

				<ul class="merlin__drawer merlin__drawer--extras">
					<?php foreach ( $links as $link ) : ?>
                        <li><?php echo wp_kses( $link, [ 'a' => [ 'href' => [], 'title' => [], 'target' => [], ], ] ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</footer>

	    <?php
	}

	/**
	 * Render svg icon by name.
	 *
	 * @since 1.0.0
	 * @access protected
     *
     * @param string $name Icon name from sprite.svg.
	 *
	 * @return void
	 **/
	private function render_icon( $name ) {

		echo wp_kses( $this->svg( [ 'icon' => $name ] ), $this->svg_allowed_html() );

    }

	/**
	 * Render green checkmark icon.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 **/
    private function render_checkmark_icon() {
	    ?>
        <svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
            <circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
        <?php
    }

} // End class Merlin.
