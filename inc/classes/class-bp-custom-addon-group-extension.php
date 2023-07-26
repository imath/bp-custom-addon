<?php
/**
 * BP Custom Add-on Component.
 *
 * @package \inc\classes\class-bp-custom-addon-group-extension
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( bp_is_active( 'groups' ) ) {
	/**
	 * BP Custom Add-on group extension Class.
	 *
	 * @since 1.0.0
	 */
	class BP_Custom_AddOn_Group_Extension extends BP_Group_Extension {
		/**
		 * Your group extension's constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$args = array(
				'slug'              => 'custom-group-extension',
				'name'              => __( 'Custom group extension', 'custom-text-domain' ),
				'nav_item_position' => 105,
				'access'            => 'anyone',
				'show_tab_callback' => array( $this, 'show_tab' ),
				'screens'           => array(
					'edit'   => array(),
					'create' => array(),
					'admin'  => array(),
				),
			);

			parent::init( $args );
		}

		/**
		 * Outputs the content of your group extension tab.
		 *
		 * @since 1.0.0
		 *
		 * @param int|null $group_id ID of the displayed group.
		 */
		public function display( $group_id = null ) {
			printf( '<p>%1$s %2$s</p>', esc_html__( 'It works! The displayed group ID is', 'custom-text-domain' ), $group_id );
		}

		/**
		 * Checks whether the main group extension’s tab should be displayed.
		 *
		 * @since 1.0.0
		 *
		 * @param int|null $group_id ID of the displayed group.
		 * @return string 'anyone' if the group extension’s tab should be displayed. 'noone' otherwise.
		 */
		public function show_tab( $group_id = null ) {
			$show_tab = 'noone';
			if ( $group_id && groups_get_groupmeta( $group_id, 'bp_custom_group_extension_is_active' ) ) {
				$show_tab = 'anyone';
			}

			return $show_tab;
		}

		/**
		 * Outputs a form to activate the extension on 'edit', 'create' & 'admin' screens.
		 *
		 * @since 1.0.0
		 *
		 * @param int|null $group_id ID of the displayed group.
		 */
		public function settings_screen( $group_id = null ) {
			$active = (int) groups_get_groupmeta( $group_id, 'bp_custom_group_extension_is_active' );
			printf(
				'<label><input type="checkbox" name="bp_custom_group_extension_is_active" value="1" %1$s>%2$s</input></label>
				<input type="hidden" name="bp_custom_group_extension_was_active" value="%3$s">',
				checked( $active, true, false ),
				esc_html__( 'I want to activate the custom group extension!', 'custom-text-domain' ),
				$active
			);
		}

		/**
		 * Activate or Deactivate the group extension from 'edit', 'create' or 'admin' screens.
		 *
		 * @since 1.0.0
		 *
		 * @param int|null $group_id ID of the displayed group.
		 */
		public function settings_screen_save( $group_id = null ) {
			$was_active = 0;
			$is_active  = 0;

			if ( isset( $_REQUEST['bp_custom_group_extension_was_active'] ) ) {
				$was_active = intval( wp_unslash( $_REQUEST['bp_custom_group_extension_was_active'] ) );

				if ( isset( $_REQUEST['bp_custom_group_extension_is_active'] ) ) {
					$is_active = intval( wp_unslash( $_REQUEST['bp_custom_group_extension_is_active'] ) );
				}

				if ( $was_active && ! $is_active ) {
					groups_delete_groupmeta( $group_id, 'bp_custom_group_extension_is_active' );
				} elseif ( ! $was_active && $is_active ) {
					groups_update_groupmeta( $group_id, 'bp_custom_group_extension_is_active', $is_active );
				}
			}
		}
	}
}
