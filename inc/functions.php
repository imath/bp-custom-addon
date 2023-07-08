<?php
/**
 * BP Custom Add-on functions.
 *
 * @package \inc\functions
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bp_custom_add_on_screen_displayed() {
	echo 'It works!';
}

function bp_custom_add_on_screen_callback() {
	bp_core_load_template( 'members/single/home' );

	add_action( 'bp_template_content', 'bp_custom_add_on_screen_displayed' );
}
//add_action( 'bp_screens', 'bp_custom_add_on_screens' );
