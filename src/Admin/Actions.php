<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Actions.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @subpackage Admin
 */
class Actions {

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_post_eac_edit_category', array( $this, 'edit_category' ) );
	}

	/**
	 * Edit category.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function edit_category() {
		check_admin_referer( 'eac_edit_category' );
		$referer  = wp_get_referer();
		$id       = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$type     = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$desc     = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
		$status   = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		$category = eac_insert_category(
			array(
				'id'          => $id,
				'name'        => $name,
				'type'        => $type,
				'description' => $desc,
				'status'      => $status,
			)
		);

		if ( is_wp_error( $category ) ) {
			EAC()->flash->error( $category->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Category saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $category->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}
}
