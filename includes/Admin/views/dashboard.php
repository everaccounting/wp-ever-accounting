<?php
/**
 * View: Admin dashboard
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Overview
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

$revenues = eac_get_revenues( array( 'limit' => 5 ) );
$expenses = eac_get_expenses( array( 'limit' => 5 ) );

?>
<div class="wrap eac-wrapper">
	<div class="eac-panel grid--fields">
		<?php
		eac_form_field( array(
			'id'          => 'name',
			'label'       => __( 'Name', 'wp-ever-accounting' ),
			'placeholder' => __( 'John Doe', 'wp-ever-accounting' ),
			'required'    => true,
			'suffix'      => 'suffix',
			'prefix'      => 'prefix',
			'tooltip'     => 'tooltip',
			'desc'        => 'desc',
		) );
		eac_form_field( array(
			'id'          => 'name',
			'label'       => __( 'Name', 'wp-ever-accounting' ),
			'placeholder' => __( 'John Doe', 'wp-ever-accounting' ),
			'required'    => true,
			'suffix'      => 'suffix',
			'prefix'      => 'prefix',
			'tooltip'     => 'tooltip',
			'desc'        => 'desc',
		) );
		?>
	</div>
	<div class="eac-panel inline--fields">
		<?php
		eac_form_field( array(
			'id'          => 'name',
			'label'       => __( 'Name', 'wp-ever-accounting' ),
			'placeholder' => __( 'John Doe', 'wp-ever-accounting' ),
			'required'    => true,
			'suffix'      => 'suffix',
			'prefix'      => 'prefix',
			'tooltip'     => 'tooltip',
			'desc'        => 'desc',
		) );
		eac_form_field( array(
			'id'          => 'name',
			'label'       => __( 'Name', 'wp-ever-accounting' ),
			'placeholder' => __( 'John Doe', 'wp-ever-accounting' ),
			'required'    => true,
			'suffix'      => 'suffix',
			'prefix'      => 'prefix',
			'tooltip'     => 'tooltip',
			'desc'        => 'desc',
		) );
		?>
	</div>
</div>
