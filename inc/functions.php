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

/**
 * Returns Add-on version.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_get_version() {
	return '1.0.0';
}

/**
 * Outputs the content of Custom pages for the displayed user.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_screen_displayed() {
	printf(
		'<p>%s</p>',
		esc_html__( 'It works!', 'custom-text-domain' )
	);

	/**
	 * Fires after the custom add-on member's tab displayed content.
	 *
	 * @since 1.0.0
	 */
	do_action( 'bp_custom_add_on_screen_displayed' );
}

/**
 * Screen function used for the Custom Add-on member navigation items.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_screen_callback() {
	/**
	 * Fires just before the Custom Add-on template is loaded.
	 *
	 * @since 1.0.0
	 */
	do_action( 'bp_custom_add_on_screen_callback' );

	bp_core_load_template( 'members/single/home' );

	add_action( 'bp_template_content', 'bp_custom_add_on_screen_displayed' );
}

/**
 * Sets the template to load for the Custom directory.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_directory_screen() {
	if ( ! bp_is_current_component( 'custom' ) || bp_is_user() ) {
		return;
	}

	bp_update_is_directory( true, 'custom' );

	// This is where you should use a custom template extending BP Templates stacks.
	bp_core_load_template( 'custom/index' );
}
add_action( 'bp_screens', 'bp_custom_add_on_directory_screen' );

/**
 * Sets the Custom Add-on directory content dummy post.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_set_dummy_post() {
	// Use the Custom Add-on directory title by default.
	$title = bp_get_directory_title( 'custom' );

	bp_theme_compat_reset_post(
		array(
			'ID'             => 0,
			'post_title'     => $title,
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'is_page'        => true,
			'comment_status' => 'closed',
		)
	);
}

/**
 * Outputs the Custom directory content.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_set_content_template() {
	/*
	 * You should use a specific template extending the BP Templates stack.
	 * eg: /path-to-your-add-on-templates/custom/index.php`.
	 *
	 * Then you'd need to buffer the template content doing:
	 * $template = bp_buffer_template_part( 'custom/index', null, false );
	 *
	 * This would let themes/template packs override your template if they need more
	 * customization.
	 */

	$template = sprintf( '<p>%s</p>', esc_html__( 'It works!', 'custom-text-domain' ) );

	if ( bp_current_item() ) {
		$template .= sprintf(
			'<p>%1$s <strong>%2$s</strong></p>',
			esc_html__( 'Current item is:', 'custom-text-domain' ),
			esc_html( bp_current_item() )
		);
	}

	if ( bp_current_action() ) {
		$template .= sprintf(
			'<p>%1$s <strong>%2$s</strong></p>',
			esc_html__( 'Current item action is:', 'custom-text-domain' ),
			esc_html( bp_current_action() )
		);
	}

	if ( bp_action_variables() ) {
		$template .= sprintf(
			'<p>%1$s <strong>%2$s</strong></p>',
			esc_html__( 'Current item action variables are:', 'custom-text-domain' ),
			implode( ', ', array_map( 'esc_html', bp_action_variables() ) )
		);
	}

	// Shows how to use the new BP Functions to build URLs.
	if ( ! bp_current_item() ) {
		/**
		 * The `bp_rewrites_get_url()` function will help you build your links for pretty or
		 * plain permalinks.
		 */

		// This is an example of item link.
		$link_to_random_component_item = bp_rewrites_get_url(
			array(
				'component_id' => 'custom', // The ID of your component, see `bp_custom_add_on_component()`.
				'single_item'  => 'random', // The slug of one of your component's single item.
			)
		);

		$template .= sprintf(
			'<p>%1$s <a href="%2$s">%3$s</a></p>',
			esc_html__( 'Visit Random item: ', 'custom-text-domain' ),
			esc_url( $link_to_random_component_item ),
			esc_html__( 'Random is here', 'custom-text-domain' ),
		);

		// This is an example of action link about an item.
		$link_to_random_component_item_action = bp_rewrites_get_url(
			array(
				'component_id'        => 'custom', // The ID of your component, see `bp_custom_add_on_component()`.
				'single_item'         => 'random', // The slug of one of your component's single item.
				'single_item_action'  => 'edit',   // The slug of one of your component's single item's action.
			)
		);

		$template .= sprintf(
			'<p>%1$s <a href="%2$s">%3$s</a></p>',
			esc_html__( 'Visit Random item action: ', 'custom-text-domain' ),
			esc_url( $link_to_random_component_item_action ),
			esc_html__( 'Random’s action is here', 'custom-text-domain' ),
		);

		// This is an example of action variables link about an item.
		$link_to_random_component_item_action_variables = bp_rewrites_get_url(
			array(
				'component_id'                 => 'custom', // The ID of your component, see `bp_custom_add_on_component()`.
				'single_item'                  => 'random', // The slug of one of your component single item.
				'single_item_action'           => 'edit',   // The slug of one of your component single item's action.
				'single_item_action_variables' => array(
					'something', 'new'
				),                                          // An array of action variable slugs for one of your component single item's action.
			)
		);

		$template .= sprintf(
			'<p>%1$s <a href="%2$s">%3$s</a></p>',
			esc_html__( 'Visit Random item action variables: ', 'custom-text-domain' ),
			esc_url( $link_to_random_component_item_action_variables ),
			esc_html__( 'Random’s action variables are here', 'custom-text-domain' ),
		);
	}

	return $template;
}

/**
 * Sets the Custom Add-on directory theme compat screens.
 *
 * @since 1.0.0
 */
function bp_custom_add_on_set_directory_theme_compat() {
	if ( bp_is_current_component( 'custom' ) && ! bp_is_user() ) {
		add_action( 'bp_template_include_reset_dummy_post_data', 'bp_custom_add_on_set_dummy_post' );
		add_filter( 'bp_replace_the_content', 'bp_custom_add_on_set_content_template' );
	}
}
add_action( 'bp_setup_theme_compat', 'bp_custom_add_on_set_directory_theme_compat' );
