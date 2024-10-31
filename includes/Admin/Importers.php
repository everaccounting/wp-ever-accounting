<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Importers class.
 *
 * @since 1.0.0
 */
class Importers {

	/**
	 * Importer constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'eac_tools_page_tabs', array( __CLASS__, 'register_tabs' ), - 1 );
		add_action( 'eac_tools_page_import_content', array( __CLASS__, 'customers_import' ) );
	}

	/**
	 * Register tabs.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		if ( current_user_can( 'manage_options' ) ) {
			$tabs['import'] = __( 'Import', 'wp-ever-accounting' );
		}

		return $tabs;
	}


	/**
	 * Export customers.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function customers_import() {
		?>
		<div class="eac-card">
			<div class="eac-card__header">
				<h2 class="eac-card__title"><?php esc_html_e( 'Import Customers', 'wp-ever-accounting' ); ?></h2>
			</div>
			<div class="eac-card__body">
				<form method="post" enctype="multipart/form-data" class="eac_importer" data-type="customers" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eac_ajax_import' ) ); ?>">
					<p>
						<?php
						printf(
							/* translators: %s: import type */
							esc_html__( 'Import customers from CSV file. Download a %1$s sample file %2$s to learn how to format the CSV file.', 'wp-ever-accounting' ),
							'<a href="' . esc_url( EAC()->get_dir_url( 'samples/import/customers.csv' ) ) . '" download>',
							'</a>'
						);
						?>
					</p>
					<div class="eac-form-field">
						<label for="file"><?php esc_html_e( 'Select file', 'wp-ever-accounting' ); ?></label>
						<input type="file" name="file" id="file" accept="text/csv" required>
					</div>
					<?php submit_button( esc_html__( 'Import', 'wp-ever-accounting' ), 'secondary', null, false ); ?>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Get importer class.
	 *
	 * @param string $type Importer type.
	 *
	 * @since 1.0.0
	 * @return Importers\Importer|null Importer class.
	 */
	public static function get_importer( $type ) {
		switch ( $type ) {
			case 'customers':
				$importer = Importers\Customers::class;
				break;

			default:
				/**
				 * Filter the import type.
				 *
				 * @since 1.0.2
				 */
				$importer = apply_filters( "eac_ajax_{$type}_importer", null );
		}

		return $importer;
	}
}
