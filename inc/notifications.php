<?php
/**
 * BP Custom Add-on Notifications functions.
 *
 * @package \inc\notifications
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format notifications related to the custom add-on.
 *
 * @since 1.0.0
 *
 * @param string $action            The notification type.
 * @param int    $item_id           The receiver’s user ID.
 * @param int    $secondary_item_id The sender's user ID.
 * @param int    $total_items       The total number of notifications to format.
 * @param string $format            'string' for notification HTML link or 'array' for separate link and text.
 * @param int    $id                Optional. The notification ID.
 * @return string|array             Formatted notification.
 */
function bp_custom_add_on_notifications_format_callback( $action, $item_id, $secondary_item_id, $total_items, $format = 'string', $id = 0 ) {
	$retval = '';

	if ( 'bp-custom-add-on-notification' === $action ) {
		$receiver    = (int) $item_id;
		$sender      = (int) $secondary_item_id;
		$total_items = (int) $total_items;

		// Use a generic link for all format.
		$link = add_query_arg( 'type', $action, bp_get_notifications_permalink() );
		$text = '';

		if ( $total_items > 1 ) {
			/* translators: 1: the number of notifications */
			$text = sprintf( esc_html__( 'You have %d new custom notifications', 'custom-text-domain' ), $total_items );

		} else {
			$text = sprintf(
				/* translators: 1: the user sender name */
				__( '%s sent you a custom notification', 'custom-text-domain' ),
				bp_core_get_user_displayname( $sender )
			);
		}

		if ( 'string' === $format ) {
			$retval = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( $link ),
				esc_html( $text )
			);
		} else {
			$retval = array(
				'text' => $text,
				'link' => $link
			);
		}
	}

	return $retval;
}

/**
 * When using BP URI globals, registering the Ajax action is required.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_notifications_register_ajax_action() {
	bp_ajax_register_action( 'bp_custom_add_on_ajax_notify' );
}
add_action( 'bp_init', 'bp_custom_add_on_notifications_register_ajax_action' );

/**
 * Registers the Notification JavaScript.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_notifications_register_script() {
	wp_register_script(
		'bp-custom-add-on-notification',
		plugins_url( 'js/notification.js', __FILE__ ),
		array(),
		bp_custom_add_on_get_version(),
		false
	);
}
add_action( 'bp_enqueue_scripts', 'bp_custom_add_on_notifications_register_script', 1 );

/**
 * Enqueues the Notification JavaScript only when needed.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_notifications_enqueue_script() {
	if ( ! bp_is_user() || ! bp_is_current_action( 'default-subnav-slug' ) || bp_is_my_profile() ) {
		return;
	}

	wp_enqueue_script( 'bp-custom-add-on-notification' );
	wp_add_inline_script(
		'bp-custom-add-on-notification',
		sprintf( 'const bpCustomAddOnAjaxURL = %s;', wp_json_encode( bp_core_ajax_url() ) ),
		'before'
	);
}
add_action( 'bp_enqueue_community_scripts', 'bp_custom_add_on_notifications_enqueue_script' );

/**
 * Outputs a link to send a custom add-on notification.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_notifications_output_send_notification_link() {
	// Only show on other users first tab.
	if ( ! bp_is_current_action( 'default-subnav-slug' ) || bp_is_my_profile() ) {
		return;
	}

	printf(
		'<p><a href="#send-notification" id="bp-custom-add-on-send-notification" data-nonce="%1$s">%2$s</a></p><p id="bp-custom-add-on-notification-result"></p>',
		wp_create_nonce( 'bp-custom-add-on-notification-nonce' ),
		esc_html__( 'Send me a Custom Add-on notification!', 'custom-text-domain' )
	);
}
add_action( 'bp_custom_add_on_screen_displayed', 'bp_custom_add_on_notifications_output_send_notification_link' );

/**
 * Ajax action callback to send a custom add-on notification
 *
 * @since 1.0.0
 */
function bp_custom_add_on_ajax_notify_callback() {
	if ( ! isset( $_POST['bp-add-on-nonce'] ) || ! $_POST['bp-add-on-nonce'] ) {
		wp_send_json_error(
			array(
				'message' => __( 'Please contact the administrator, this Ajax request is not secured the right way.', 'custom-text-domain' ),
			)
		);
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['bp-add-on-nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'bp-custom-add-on-notification-nonce' ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Please contact the administrator, this Ajax request is missing the secure token.', 'custom-text-domain' ),
			)
		);
	}

	$receiver = bp_displayed_user_id();
	$sender   = bp_loggedin_user_id();

	if ( ! $receiver ) {
		wp_send_json_error(
			array(
				'message' => __( 'Please contact the administrator, this Ajax action is not registered the right way.', 'custom-text-domain' ),
			)
		);
	}

	$notification = bp_notifications_add_notification(
		array(
			'user_id'           => $receiver,
			'item_id'           => $receiver,
			'secondary_item_id' => $sender,
			'component_name'    => buddypress()->custom->id,
			'component_action'  => 'bp-custom-add-on-notification',
			'date_notified'     => bp_core_current_time(),
			'is_new'            => 1,
		)
	);

	if ( ! $notification ) {
		wp_send_json_error(
			array(
				'message' => __( 'Ouch! Sending the notification failed.', 'custom-text-domain' ),
			)
		);
	} else {
		wp_send_json_success(
			array(
				'message' => __( 'Notification sent!', 'custom-text-domain' ),
			)
		);
	}
}
add_action( 'wp_ajax_bp_custom_add_on_ajax_notify', 'bp_custom_add_on_ajax_notify_callback' );

/**
 * Edit the BP Nouveau Ajax query string if needed.
 *
 * @since 1.0.0
 *
 * @param string $query_string The Ajax query string used by BP Loops.
 * @param string $object       The type of BP Loop tp run.
 * @return string The Ajax query string used by BP Loops.
 */
function bp_custom_add_on_notifications_ajax_querystring( $query_string, $object ) {
	if ( 'notifications' === $object ) {
		$qs            = bp_parse_args( $query_string, array() );
		$referer_query = wp_parse_url( wp_get_referer(), PHP_URL_QUERY );

		if ( $referer_query && false !== strpos( $referer_query, 'type=' ) ) {
			$qs['component_action'] = str_replace( 'type=', '', $referer_query );
		}

		$query_string = http_build_query( $qs );
	}

	return $query_string;
}
add_filter( 'bp_nouveau_ajax_querystring', 'bp_custom_add_on_notifications_ajax_querystring', 10, 2 );
