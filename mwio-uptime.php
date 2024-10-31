<?php

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/*
Plugin Name: My Website is Online - Uptime monitoring
Description: Don’t let downtimes impact your business.
Version: 1.0.1
Requires at least: 5.0
Author: My Website is Online
Author URI: https://mywebsiteisonline.com/
Domain Path: /languages
Text Domain: mwio-uptime
*/

if ( ! function_exists( 'mywebsiteisonline' ) && ! class_exists( 'My_Website_Is_Online' ) ) {

	class My_Website_Is_Online {

		public function __construct() {
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
		}

		public function register_settings() {
			register_setting( 'mwio_uptime', 'mwio_uptime_website_code' );
		}

		public function admin_menu() {
			ob_start();
			readfile( plugin_dir_path( __FILE__ ) . 'images/icon.svg' );
			$icon = ob_get_clean();

			add_menu_page(
				__( 'My Website is Online', 'mwio-uptime' ),
				__( 'My Website is Online', 'mwio-uptime' ),
				'manage_options',
				'mwio-uptime',
				array( $this, 'page' ),
				'data:image/svg+xml;base64,' . base64_encode( $icon )
			);
		}

		public function page() {
			?>
            <div class="wrap">
                <h2><?php _e( 'My Website is Online', 'mwio-uptime' ); ?></h2>
				<?php settings_errors(); ?>
                <p><?php _e( 'You can validate that the configuration works by visiting this link:', 'mwio-uptime' ) ?>
                    <a target="_blank" rel="nofollow"
                       href="<?php esc_attr_e( rest_url( 'mywebsiteisonline/v1/verify' ) ) ?>"><?php esc_html_e( rest_url( 'mywebsiteisonline/v1/verify' ) ) ?></a>
                </p>
                <form method="post" action="options.php">
                    <fieldset>
						<?php
						settings_fields( 'mwio_uptime' );
						do_settings_sections( 'mwio_uptime' );
						?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e( 'Website code', 'mwio-uptime' ); ?></th>
                                <td>
                                    <input type="text" class="regular-text" name="mwio_uptime_website_code"
                                           value="<?php esc_attr_e( get_option( 'mwio_uptime_website_code' ) ); ?>"/>
                                </td>
                            </tr>
                        </table>
                        <p class="submit">
							<?php submit_button( null, 'primary', 'submit', false ); ?>
                        </p>
                    </fieldset>
                </form>
            </div>
			<?php
		}

		public function rest_api_init() {
			register_rest_route( 'mywebsiteisonline/v1', '/verify', array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'output_code' ),
				'permission_callback' => '__return_true'
			) );
		}

		public function output_code() {
			return new WP_REST_Response( [
				'code' => esc_attr( get_option( 'mwio_uptime_website_code' ) )
			] );
		}

	}

	function mywebsiteisonline() {
		static $plugin;

		if ( ! isset( $plugin ) ) {
			$plugin = new My_Website_Is_Online();
		}

		return $plugin;
	}

	mywebsiteisonline();

}

