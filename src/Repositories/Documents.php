<?php
/**
 * Invoice repository.
 *
 * Handle invoice insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;
use EverAccounting\Models\Document;
use EverAccounting\Models\DocumentItem;

defined( 'ABSPATH' ) || exit;

/**
 * Class Accounts
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Documents extends ResourceRepository {
	/**
	 * @var string
	 */
	const TABLE = 'ea_documents';

	/**
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $table = self::TABLE;

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data_type = array(
		'id'              => '%d',
		'document_number' => '%s',
		'type'            => '%s',
		'order_number'    => '%s',
		'status'          => '%s',
		'issue_date'      => '%s',
		'due_date'        => '%s',
		'payment_date'    => '%s',
		'category_id'     => '%d',
		'contact_id'      => '%d',
		'address'         => '%s',
		'currency_code'   => '%s',
		'currency_rate'   => '%f',
		'subtotal'        => '%f',
		'discount'        => '%f',
		'discount_type'   => '%s',
		'total_tax'       => '%f',
		'total_discount'  => '%f',
		'total'           => '%f',
		'tax_inclusive'   => '%d',
		'terms'           => '%s',
		'attachment_id'   => '%d',
		'key'             => '%s',
		'parent_id'       => '%d',
		'creator_id'      => '%d',
		'date_created'    => '%s',
	);


	/**
	 * Get the next available number.
	 *
	 * @param Document $document
	 * @since 1.1.0
	 * @return int
	 */
	public function get_next_number( &$document ) {
		global $wpdb;
		$max = (int) $wpdb->get_var( $wpdb->prepare( "select max(id) from {$wpdb->prefix}ea_documents WHERE type=%s", $document->get_type() ) );
		return $max + 1;
	}

	/**
	 * Read order items of a specific type from the database for this order.
	 *
	 * @param Document $document Order object.
	 *
	 * @return array
	 */
	public function get_items( $document ) {
		global $wpdb;

		// Get from cache if available.
		$items = 0 < $document->get_id() ? wp_cache_get( 'document-item-' . $document->get_id(), 'ea-document-items' ) : false;

		if ( false === $items ) {
			$items = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_document_items WHERE document_id = %d ORDER BY id;", $document->get_id() )
			);
			foreach ( $items as $item ) {
				wp_cache_set( 'document-item-' . $item->id, $item, 'ea-document-items' );
			}
			if ( 0 < $document->get_id() ) {
				wp_cache_set( 'document-item' . $document->get_id(), $items, 'ea-document-items' );
			}
		}
		$results = array();
		foreach ( $items as $item ) {
			$results[ absint( $item->item_id ) ] = new DocumentItem( $item );
		}

		return $results;
	}

	/**
	 * Delete Invoice Items.
	 *
	 * @since 1.1.0
	 *
	 * @param $item
	 */
	public function delete_items( $item ) {
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . DocumentItems::TABLE, array( 'document_id' => $item->get_id() ) );
	}

	/**
	 * Delete Invoice notes.
	 *
	 * @since 1.1.0
	 *
	 * @param $item
	 */
	public function delete_notes( $item ) {
		global $wpdb;
		$wpdb->delete(
			$wpdb->prefix . Notes::TABLE,
			array(
				'parent_id' => $item->get_id(),
				'type'      => $item->get_type(),
			)
		);
	}

	public function delete_transactions( $item ) {
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . Transactions::TABLE, array( 'document_id' => $item->get_id() ) );
	}

	/**
	 * Delete items.
	 *
	 * @param \EverAccounting\Abstracts\ResourceModel $item
	 * @param array                                   $args
	 * @since 1.1.0
	 */
	public function delete( &$item, $args = array() ) {
		$this->delete_items( $item );
		$this->delete_notes( $item );
		$this->delete_transactions( $item );
		parent::delete( $item, $args );
	}
}
