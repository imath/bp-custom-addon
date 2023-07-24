<?php
/**
 * BP Custom Add-on Component.
 *
 * @package \inc\classes\class-bp-custom_addon-component
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BP Custom Add-on Component Class.
 *
 * @since 1.0.0
 */
class BP_Custom_AddOn_Component extends BP_Component {
	/**
	 * Your component's constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::start(
			// Your component ID.
			'custom',

			// Your component Name.
			__( 'Custom component', 'custom-text-domain' ),

			// The path from where additional files should be included.
			plugin_dir_path( dirname( __FILE__ ) ),

			// Additional parameters.
			array(
				'adminbar_myaccount_order' => 100,
				'features'                 => array( 'feature-one', 'feature-two' ),
				'search_query_arg'         => 'custom-component-search',
			)
		);
	}

	/**
	 * Your component global variables (BP Ones and your component ones).
	 *
	 * @see BP_Component::setup_globals() for a description of arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args See BP_Component::setup_globals() for a description.
	 */
	public function setup_globals( $bp_globals = array() ) {
		parent::setup_globals(
			array(
				'slug'            => 'custom-slug',

				// This what comes after your `site_url()`.
				'root_slug'       => 'custom-directory-slug',

				// I confirm my component has a directory page.
				'has_directory'   => true,

				// This is new in BuddyPress 12.0.0.
				'rewrite_ids'     => array(
					'directory'                    => 'custom_directory',
					'single_item'                  => 'custom_item',
					'single_item_action'           => 'custom_item_action',
					'single_item_action_variables' => 'custom_item_action_variables',
				),
				'directory_title' => __( 'Custom directory', 'custom-text-domain' ),
				'search_string'   => __( 'Search custom items', 'custom-text-domain' ),
			)
		);
	}

	/**
	 * Include your component's required files.
	 *
	 * @since 1.0.0
	 *
	 * @param array $files An array of file names located into `$this->path`.
	 *                     NB: `$this->path` in this example is `/wp-content/plugins/bp-custom/inc`
	 */
	public function includes( $files = array() ) {
		parent::includes(
			array(
				'functions.php',
			)
		);
	}

	/**
	 * Register your component‘s navigation.
	 *
	 * @since 1.0.0
	 *
	 * @param array $main_nav Associative array
	 * @param array $sub_nav  Optional. Multidimensional Associative array.
	 */
	public function register_nav( $main_nav = array(), $sub_nav = array() ) {
		parent::register_nav(
			array(
				'name'                => $this->name,
				'slug'                => $this->slug,
				'position'            => 100,
				'screen_function'     => 'bp_custom_add_on_screen_callback',
				'default_subnav_slug' => 'default-subnav-slug',
				'item_css_id'         => $this->id,
			),
			array(
				array(
					'name'            => __( 'Default sub nav name', 'custom-text-domain' ),
					'slug'            => 'default-subnav-slug',
					'parent_slug'     => $this->slug,
					'position'        => 10,
					'screen_function' => 'bp_custom_add_on_screen_callback',
					'item_css_id'     => 'default-subnav-' . $this->id,
				),
				array(
					'name'                     => __( 'Other sub nav name', 'custom-text-domain' ),
					'slug'                     => 'other-subnav-slug',
					'parent_slug'              => $this->slug,
					'position'                 => 20,
					'screen_function'          => 'bp_custom_add_on_screen_callback',
					'item_css_id'              => 'other-subnav-' . $this->id,

					// Let's restrict this page to members viewing their own profile.
					'user_has_access_callback' => 'bp_is_my_profile',
				),
			)
		);
	}

	/**
	 * Set up the component entries in the WordPress Admin Bar.
	 *
	 * @since 1.0.0
	 *
	 * @param array $wp_admin_bar A multidimensional array of nav item arguments.
	 */
	public function setup_admin_bar( $wp_admin_bar = array() ) {
		if ( is_user_logged_in() ) {

			// Add the "Custom" sub menu.
			$wp_admin_bar[] = array(
				'parent' => buddypress()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => _x( 'Custom', 'My Account Custom sub nav', 'custom-text-domain' ),
				'href'   => bp_loggedin_user_url( bp_members_get_path_chunks( array( $this->slug ) ) ),
			);

			// Add the "Default sub nav" sub menu.
			$wp_admin_bar[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . 'default-sub-nav',
				'title'    => _x( 'Default sub nav name', 'My Account Custom sub nav', 'custom-text-domain' ),
				'href'     => bp_loggedin_user_url( bp_members_get_path_chunks( array( $this->slug, 'default-subnav-slug' ) ) ),
				'position' => 10,
			);

			// Add the "Other sub nav" sub menu.
			$wp_admin_bar[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . 'other-sub-nav',
				'title'    => _x( 'Other sub nav name', 'My Account Custom sub nav', 'custom-text-domain' ),
				'href'     => bp_loggedin_user_url( bp_members_get_path_chunks( array( $this->slug, 'other-subnav-slug' ) ) ),
				'position' => 20,
			);
		}

		parent::setup_admin_bar( $wp_admin_bar );
	}

	/**
	 * Parse the WP_Query and eventually display the component's directory or single item.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query Required. See BP_Component::parse_query() for
	 *                        description.
	 */
	public function parse_query( $query ) {
		if ( 1 === (int) $query->get( $this->rewrite_ids['directory'] ) ) {
			$bp = buddypress();

			// Set the Custom component as current.
			$bp->current_component = 'custom';

			$custom_item_slug = $query->get( $this->rewrite_ids['single_item'] );

			// Set the Custom component current item.
			if ( $custom_item_slug ) {
				$bp->current_item = $custom_item_slug;

				$current_action = $query->get( $this->rewrite_ids['single_item_action'] );

				// Set the Custom component current item action.
				if ( $current_action ) {
					$bp->current_action = $current_action;
				}

				$action_variables = $query->get( $this->rewrite_ids['single_item_action_variables'] );

				// Set the Custom component current item action variables.
				if ( $action_variables ) {
					if ( ! is_array( $action_variables ) ) {
						$bp->action_variables = explode( '/', ltrim( $action_variables, '/' ) );
					} else {
						$bp->action_variables = $action_variables;
					}
				}
			}

			// Set the BuddyPress queried object.
			if ( isset( $bp->pages->custom->id ) ) {
				$query->queried_object    = get_post( $bp->pages->custom->id );
				$query->queried_object_id = $query->queried_object->ID;
			}
		}

		parent::parse_query( $query );
	}
}
