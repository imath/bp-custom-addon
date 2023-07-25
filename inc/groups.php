<?php
/**
 * BP Custom Add-on groups functions.
 *
 * @package \inc\groups
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the custom group extension.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_register_group_extension() {
	bp_register_group_extension( 'BP_Custom_AddOn_Group_Extension' );
}
add_action( 'bp_init', 'bp_custom_add_on_register_group_extension' );
