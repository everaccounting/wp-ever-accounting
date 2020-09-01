<?php
/**
 * Handle Customer Import.
 *
 * @since       1.0.2
 * @subpackage  Abstracts
 * @package     EverAccounting
 */

namespace EverAccounting\Import;

use EverAccounting\Abstracts\CSV_Importer;

defined( 'ABSPATH' ) || exit();


class Customer_CSV_Import extends CSV_Importer {

	/**
	 * Process a single item and save.
	 *
	 * @param array $data Raw CSV data.
	 *
	 * @throws \EverAccounting\Exception If item cannot be processed.
	 * @return array|\WP_Error
	 */
	protected function process_item( $data ) {
		try {
			do_action( 'woocommerce_product_import_before_process_item', $data );
			$data = apply_filters( 'woocommerce_product_import_process_item_data', $data );

			// Get product ID from SKU if created during the importation.
			if ( empty( $data['id'] ) && ! empty( $data['sku'] ) ) {
				$product_id = wc_get_product_id_by_sku( $data['sku'] );

				if ( $product_id ) {
					$data['id'] = $product_id;
				}
			}

			$object   = $this->get_product_object( $data );
			$updating = false;

			if ( is_wp_error( $object ) ) {
				return $object;
			}

			if ( $object->get_id() && 'importing' !== $object->get_status() ) {
				$updating = true;
			}

			if ( 'external' === $object->get_type() ) {
				unset( $data['manage_stock'], $data['stock_status'], $data['backorders'], $data['low_stock_amount'] );
			}

			if ( 'variation' === $object->get_type() ) {
				if ( isset( $data['status'] ) && -1 === $data['status'] ) {
					$data['status'] = 0; // Variations cannot be drafts - set to private.
				}
			}

			if ( 'importing' === $object->get_status() ) {
				$object->set_status( 'publish' );
				$object->set_slug( '' );
			}

			$result = $object->set_props( array_diff_key( $data, array_flip( array( 'meta_data', 'raw_image_id', 'raw_gallery_image_ids', 'raw_attributes' ) ) ) );

			if ( is_wp_error( $result ) ) {
				throw new Exception( $result->get_error_message() );
			}

			if ( 'variation' === $object->get_type() ) {
				$this->set_variation_data( $object, $data );
			} else {
				$this->set_product_data( $object, $data );
			}

			$this->set_image_data( $object, $data );
			$this->set_meta_data( $object, $data );

			$object = apply_filters( 'woocommerce_product_import_pre_insert_product_object', $object, $data );
			$object->save();

			do_action( 'woocommerce_product_import_inserted_product_object', $object, $data );

			return array(
				'id'      => $object->get_id(),
				'updated' => $updating,
			);
		} catch ( Exception $e ) {
			return new WP_Error( 'woocommerce_product_importer_error', $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}
}
