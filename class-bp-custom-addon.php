<?php
/**
 * BP Custom Add-on is an example of BuddyPress Add-on.
 *
 * @package   BP Custom Add-on
 * @author    imath
 * @license   GPL-2.0+
 * @link      https://imathi.eu
 *
 * @buddypress-plugin
 * Plugin Name:       BP Custom Add-on
 * Plugin URI:        https://github.com/imath/bp-custom-addon
 * Description:       BP Custom Add-on is an example of BuddyPress Add-on.
 * Version:           1.0.0-alpha
 * Author:            imath
 * Author URI:        https://imathi.eu
 * Text Domain:       bp-custom-addon
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages/
 * GitHub Plugin URI: https://github.com/imath/bp-custom-addon
 * Requires at least: 6.2
 * Requires PHP:      5.6
 * Requires Plugins:  buddypress
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BP Custom Add-on Main Class
 *
 * @since 1.0.0
 */
class BP_Custom_AddOn {
	/**
	 * Plugin Main Instance.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	protected static $instance = null;

	/**
	 * Checks whether BuddyPress is active.
	 *
	 * @since 1.0.0
	 */
	public static function is_buddypress_active() {
		$bp_plugin_basename   = 'buddypress/bp-loader.php';
		$is_buddypress_active = false;
		$sitewide_plugins     = (array) get_site_option( 'active_sitewide_plugins', array() );

		if ( $sitewide_plugins ) {
			$is_buddypress_active = isset( $sitewide_plugins[ $bp_plugin_basename ] );
		}

		if ( ! $is_buddypress_active ) {
			$plugins              = (array) get_option( 'active_plugins', array() );
			$is_buddypress_active = in_array( $bp_plugin_basename, $plugins, true );
		}

		return $is_buddypress_active;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 */
	public static function start() {
		// This plugin is only usable with the genuine BuddyPress.
		if ( ! self::is_buddypress_active() ) {
			return false;
		}

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Include the plugin files.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Autoload Classes.
		spl_autoload_register( array( $this, 'autoload' ) );

		// Load the Component's loader.
		require plugin_dir_path( __FILE__ ) . 'inc/loader.php';
	}

	/**
	 * Class Autoload function
	 *
	 * @since  1.0.0
	 *
	 * @param  string $class The class name.
	 */
	public function autoload( $class ) {
		$name = str_replace( '_', '-', strtolower( $class ) );

		if ( 0 !== strpos( $name, 'bp-custom-addon' ) ) {
			return;
		}

		$path = plugin_dir_path( __FILE__ ) . "inc/classes/class-{$name}.php";

		// Sanity check.
		if ( ! file_exists( $path ) ) {
			return;
		}

		require $path;
	}

	/**
	 * Displays an admin notice to explain how to activate BP Custom Add-on.
	 *
	 * @since 1.0.0
	 */
	public static function admin_notice() {
		if ( self::is_buddypress_active() ) {
			return false;
		}

		$bp_plugin_link = sprintf( '<a href="%s">BuddyPress</a>', esc_url( _x( 'https://wordpress.org/plugins/buddypress', 'BuddyPress WP plugin directory URL', 'bp-attachments' ) ) );

		printf(
			'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
			sprintf(
				/* translators: 1. is the link to the BuddyPress plugin on the WordPress.org plugin directory. */
				esc_html__( 'BP Custom Add-on requires the %1$s plugin to be active. Please deactivate BP Custom Add-on, activate %1$s and only then, reactivate BP Custom Add-on.', 'bp-attachments' ),
				$bp_plugin_link // phpcs:ignore
			)
		);
	}
}

/**
 * Let's start !
 *
 * @since 1.0.0
 */
function bp_custom_add_on() {
	return BP_Custom_AddOn::start();
}
add_action( 'bp_loaded', 'bp_custom_add_on', 1 );

// Displays a notice to inform BP Custom Add-on needs to be activated after BuddyPress.
add_action( 'admin_notices', array( 'BP_Custom_AddOn', 'admin_notice' ) );
