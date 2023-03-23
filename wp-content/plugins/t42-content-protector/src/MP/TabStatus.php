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
 * SINGLETON: Class used to implement Status tab on plugin settings page.
 *
 * @since 1.0.0
 * @author Alexander Khmelnitskiy
 **/
final class TabStatus extends Tab {

	/**
	 * The one true TabStatus.
	 *
	 * @var TabStatus
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Generate Status Tab.
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

	}

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
                $this->render_system_requirements();

                /** Render Privacy Notice. */
                $this->render_privacy_notice();
                ?>
            </div>
        </div>
		<?php

	}

	/**
	 * Render "System Requirements" field.
	 *
	 * @since 1.0.0
	 * @access public
     *         
     * @return void        
	 **/
	public function render_system_requirements() {

        $reports = [];

        /** Add Server Reporter. */
        if ( Plugin::get_tabs( 'status:reports:server:enabled' ) ) {
	        $reports[ 'server' ] = ReporterServer::get_instance();
        }

        /** Add WordPress Reporter. */
        if ( Plugin::get_tabs( 'status:reports:wordpress:enabled' ) ) {
	        $reports[ 'wordpress' ] = ReporterWordPress::get_instance();
        }

        /** Add Plugins Reporter. */
        if ( Plugin::get_tabs( 'status:reports:plugins:enabled' ) ) {
	        $reports[ 'plugins' ] = ReporterPlugins::get_instance();
        }

        /** Add Theme Reporter. */
        if ( Plugin::get_tabs( 'status:reports:theme:enabled' ) ) {
	        $reports[ 'theme' ] = ReporterTheme::get_instance();
        }

		?>

		<div class="t42-system-requirements">

			<?php foreach ( $reports as $key => $report ) : ?>
                <div class="t42-plugin-<?php echo esc_attr( $key ); ?>">
                    <div class="t42-overflow-container">
                        <table class="t42-system-requirements-table t42-table t42-table-hover t42-table-striped">
                            <thead>
                                <tr>
                                    <th colspan="3"><h3 class="t42-margin-remove"><?php echo esc_html( $report->get_title() ); ?></h3></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ( $report->get_report() as $row ) {

                                if ( is_array( $row['value'] ) ) {

                                    $this->render_array( $row['value'] );

                                } else {

                                    $this->render_string( $row );

                                }

                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>

		</div><?php

	}

	/**
	 * Render row if value is string.
	 *
	 * @param array $row - Report row.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	private function render_string( $row ) {
	    ?>
        <tr>
            <td class="t42-row-label"><?php echo esc_html( $row['label'] ); ?>:</td>
            <td>
                <span class="t42-system-value">
                    <?php
                    echo wp_kses(
                            $row['value'],
                            [
                                'span' => [
                                    't42-icon'  => true,
                                ]
                            ]
                         );
                    ?>
                </span></td>
            <td class="t42-text-left">
				<?php if ( isset( $row['warning'] ) && $row['warning'] ) : ?>
                    <i class="t42-icon-warning"></i>
					<?php echo ( isset( $row['recommendation'] ) ? esc_html( $row['recommendation'] ) : ''); ?>
				<?php endif; ?>
            </td>
        </tr>
        <?php
    }

	/**
	 * Render row if value is array.
	 *
	 * @param array $row - Report row.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	private function render_array( $row ) {

		foreach ( $row as $plugin_info ) { ?>
            <tr>
                <td>
					<?php
					if ( $plugin_info['PluginURI'] ) {
						echo "<a href='" . esc_url( $plugin_info['PluginURI'] ) . "'>" . esc_html__( $plugin_info['Name'] ) . "</a>";
					} else {
						esc_html_e( $plugin_info['Name'] );
					}

					if ( $plugin_info['Version'] ) { echo ' - ' . esc_html__( $plugin_info['Version'] ); }
					?>
                </td>
                <td>
					<?php
					if ( $plugin_info['Author'] ) {

						echo "By ";

						if ( $plugin_info['AuthorURI'] ) {
							echo "<a href='" . esc_url( $plugin_info['AuthorURI'] ) . "'>" . esc_html__( $plugin_info['Author'] ) . "</a>";
						} else {
							esc_html_e( $plugin_info['Author'] );
						}
					}
					?>
                </td>
            </tr>
		<?php
        }

	}

	/**
	 * Render Privacy Notice.
	 *
	 * @since 1.0.0
	 * @access public
     *         
     * @return void        
	 **/
	public function render_privacy_notice() {
	    ?>
        <div class="t42-alert">
	        <?php esc_html_e( 'Some data will be sent to our server to verify purchase and to ensure that a plugin is compatible with your install. We will never collect any confidential data. All data is stored anonymously.', 't42-content-protector' ); ?>
        </div>
        <?php
    }

	/**
	 * Main TabStatus Instance.
	 *
	 * Insures that only one instance of TabStatus exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
	 * @access public
     *
	 * @return TabStatus
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

} // End Class TabStatus.
