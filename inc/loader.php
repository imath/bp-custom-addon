<?php
/**
 * BP Custom Add-on component Loader.
 *
 * @package \inc\loader
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set up the BP Custom Add-on component.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_component() {
	buddypress()->custom = new BP_Custom_AddOn_Component();
}
add_action( 'bp_setup_components', 'bp_custom_add_on_component' );

function bp_custom_add_on_include() {
	?><pre><?php
	print_r( buddypress()->loaded_components ); ?></pre><?php
}
//add_action( 'bp_setup_globals', 'bp_custom_add_on_include', 40 );
