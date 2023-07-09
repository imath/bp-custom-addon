<?php

class BP_Custom_AddOn_Component extends BP_Component {
	/**
	 * Your component's constructor.
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
	 * Include your component's required files.
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
	 * Get the user logged in URL in BuddyPress >= 12.0.0 and older ones.
	 *
	 * @param array $path_chunks {
	 *     An array of arguments. Optional.
	 *
	 *     @type string $single_item_component        The component slug the action is relative to.
	 *     @type string $single_item_action           The slug of the action to perform.
	 *     @type array  $single_item_action_variables An array of additional informations about the action to perform.
	 * }
	 * @return string The logged in user URL.
	 */
	public function get_loggedin_user_url( $path_chunks = array() ) {
		$user_url = '';

		// BuddyPress 12.0.0 is being used.
		if ( function_exists( 'bp_core_get_query_parser' ) ) {
			$user_url = bp_loggedin_user_url( bp_members_get_path_chunks( $path_chunks ) );

			// An older version of BuddyPress is being used
		} else {
			$user_url = bp_loggedin_user_domain();

			if ( $path_chunks ) {
				$action_variables = end( $path_chunks );
				if ( is_array( $action_variables ) ) {
					array_pop( $path_chunks );
					$path_chunks = array_merge( $path_chunks, $action_variables );
				}

				$user_url = trailingslashit( $user_url ) . trailingslashit( implode( '/', $path_chunks ) );
			}
		}

		return $user_url;
	}

	/**
	 * Set up the component entries in the WordPress Admin Bar.
	 *
	 * @since BuddyPress 1.5.0
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
				'href'   => $this->get_loggedin_user_url( array( $this->slug ) ),
			);

			// Add the "Default sub nav" sub menu.
			$wp_admin_bar[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . 'default-sub-nav',
				'title'    => _x( 'Default sub nav name', 'My Account Custom sub nav', 'custom-text-domain' ),
				'href'     => $this->get_loggedin_user_url( array( $this->slug, 'default-subnav-slug' ) ),
				'position' => 10,
			);

			// Add the "Other sub nav" sub menu.
			$wp_admin_bar[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . 'other-sub-nav',
				'title'    => _x( 'Other sub nav name', 'My Account Custom sub nav', 'custom-text-domain' ),
				'href'     => $this->get_loggedin_user_url( array( $this->slug, 'other-subnav-slug' ) ),
				'position' => 20,
			);
		}

		parent::setup_admin_bar( $wp_admin_bar );
	}
}
