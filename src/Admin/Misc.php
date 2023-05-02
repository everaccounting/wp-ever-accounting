<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Misc.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Misc extends \EverAccounting\Singleton {

	/**
	 * Misc constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'ever_accounting_settings_tab_currencies', array( __CLASS__, 'output_currencies_tab' ) );
		add_action( 'ever_accounting_settings_tab_categories', array( __CLASS__, 'output_categories_tab' ) );
		add_action( 'ever_accounting_settings_tab_tax', array( __CLASS__, 'output_tax_tab' ) );
		add_action( 'admin_footer', array( __CLASS__, 'output_category_modal' ) );
	}

	/**
	 * Output the banking page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
	}

	/**
	 * Output the currencies tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_currencies_tab1() {
		$action      = eac_filter_input( INPUT_GET, 'action' );
		$currency_id = eac_filter_input( INPUT_GET, 'currency_id', 'absint' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/currencies/edit-currency.php';
		} else {
			include dirname( __FILE__ ) . '/views/currencies/list-currencies.php';
		}
	}

	/**
	 * Output the currencies tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_currencies_tab() {
		// What I want to do is wi
		$rates      = get_option( 'eac_currency_rates', array() );
		$base       = get_option( 'eac_get_base_currency', 'USD' );
		$currencies = include ever_accounting()->get_path( 'i18n/currencies.php' );
		if ( empty( $rates ) ) {
			$rates = array(
				$base => 1,
			);
		}
		?>
		<table class="widefat striped fixed ">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Symbol', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Conversion Rate', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $rates as $currency => $rate ) : ?>
					<tr>
						<td><?php echo esc_html( $currency ); ?></td>
						<td><?php echo esc_html( $currencies[ $currency ]['symbol'] ); ?></td>
						<td><?php echo esc_html( $rate ); ?></td>
						<td>
							<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit', 'currency_id' => $currency ) ) ); ?>"><?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?></a>
							<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'delete', 'currency_id' => $currency ) ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Output the categories tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_categories_tab() {
		$action      = eac_filter_input( INPUT_GET, 'action' );
		$category_id = eac_filter_input( INPUT_GET, 'category_id', 'absint' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/categories/edit-category.php';
		} else {
			include dirname( __FILE__ ) . '/views/categories/list-categories.php';
		}
	}

	/**
	 * Output the category modal.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_category_modal() {
		$category = new \EverAccounting\Models\Category();
		?>
		<script type="text/template" id="eac-category-modal" data-title="<?php esc_html_e( 'Add Category', 'wp-ever-accounting' ); ?>">
			<?php require __DIR__ . '/views/categories/category-form.php'; ?>
		</script>
		<?php
	}

	/**
	 * Output the tax tab.
	 *
	 * @param string $section Current section.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_tax_tab( $section ) {
		if ( 'tax_rates' !== $section ) {
			return;
		}
		$tax_rates = get_option(
			'eac_tax_rates',
			array(
				array(
					'name'     => 'Standard',
					'compound' => 'yes',
					'rate'     => '20',
				),
			)
		);
		$columns   = array(
			'name'     => __( 'Name', 'wp-ever-accounting' ),
			'rate'     => __( 'Rate', 'wp-ever-accounting' ),
			'compound' => __( 'Compound', 'wp-ever-accounting' ),
		);

		include dirname( __FILE__ ) . '/views/settings/tax-rates.php';

		return;
		// Show a list table with these rates. and at the bottom, a button to add a new rate.
		?>
		<form id="eac-tax-rates-form" method="post">
			<table class="fixed striped widefat eac-mt-20">
				<thead>
				<tr>
					<?php foreach ( $columns as $column => $label ) : ?>
						<th><?php echo esc_html( $label ); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>
				<tbody>
				<?php foreach ( $tax_rates as $tax_rate ) : ?>
					<tr>
						<?php foreach ( $columns as $column => $label ) : ?>
							<!--rate field is editable-->
							<!--compound field is a checkbox-->
							<!--name field is editable-->
							<?php if ( 'name' === $column ) : ?>
								<td>
									<input type="text" name="tax_rates[<?php echo esc_attr( $column ); ?>][]" value="<?php echo esc_attr( $tax_rate[ $column ] ); ?>"/>
								</td>
							<?php elseif ( 'compound' === $column ) : ?>
								<td>
									<input type="checkbox" name="tax_rates[<?php echo esc_attr( $column ); ?>][]" value="yes" <?php checked( 'yes', $tax_rate[ $column ] ); ?> />
								</td>
							<?php else : ?>
								<td>
									<input type="text" name="tax_rates[<?php echo esc_attr( $column ); ?>][]" value="<?php echo esc_attr( $tax_rate[ $column ] ); ?>"/>
								</td>
							<?php endif; ?>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<button type="button" class="button button-primary eac-mt-20" id="eac-add-tax-rate"><?php esc_html_e( 'Add Tax Rate', 'wp-ever-accounting' ); ?></button>
		</form>
		<script>
			// when click on add tax rate, add a new row to the table.
			jQuery(document).ready(function ($) {
				$('#eac-add-tax-rate').on('click', function () {
					var $table = $('#eac-tax-rates-form table');
					var $row = $('<tr></tr>');
					var columns = <?php echo wp_json_encode( $columns ); ?>;
					$.each(columns, function (column, label) {
						var $cell = $('<td></td>');
						if ('name' === column) {
							$cell.append('<input type="text" name="tax_rates[' + column + '][]" />');
						} else if ('compound' === column) {
							$cell.append('<input type="checkbox" name="tax_rates[' + column + '][]" value="yes" />');
						} else {
							$cell.append('<input type="text" name="tax_rates[' + column + '][]" />');
						}
						$row.append($cell);
					});
					$table.find('tbody').append($row);
				});
			});
		</script>

		<?php
	}
}
