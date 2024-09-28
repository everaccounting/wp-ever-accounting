<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Dashboard
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 */
class Dashboard {

	/**
	 * Dashboard constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'eac_dashboard_page', array( __CLASS__, 'render' ) );
	}

	/**
	 * Render dashboard.
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		?>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Dashboard', 'wp-ever-accounting' ); ?></h1>

		<div class="eac-card">
			<div class="eac-card__header">
				<?php esc_html_e( 'Overview', 'wp-ever-accounting' ); ?>
			</div>

			<div class="eac-card__body">
				<p><?php esc_html_e( 'Welcome to Ever Accounting!', 'wp-ever-accounting' ); ?></p>
			</div>
		</div>

		<div class="eac-stats stats--3">
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Sales', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value">24.00</div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Expenses', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value">24.00</div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Profits', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value">24.00</div>
			</div>
		</div>

		<div class="eac-grid cols-2">
			<div class="eac-card">
				<div class="eac-card__header">
					<?php esc_html_e( 'Payment Categories', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>

			<div class="eac-card">
				<div class="eac-card__header">
					<?php esc_html_e( 'Expense Categories', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>
		</div>

		<div class="eac-grid cols-3">
			<div class="eac-card">
				<div class="eac-card__header">
					<?php esc_html_e( 'Recent Payments', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>

			<div class="eac-card">
				<div class="eac-card__header">
					<?php esc_html_e( 'Recent Expenses', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>

			<div class="eac-card">
				<div class="eac-card__header">
					<?php esc_html_e( 'Account Balances', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>
		</div>

		<?php
	}
}
