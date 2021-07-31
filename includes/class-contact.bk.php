<?php
/**
 * Handle the Contact object.
 *
 * @package     EverAccounting
 * @class       Contact
 * @version     1.2.1
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Contact object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 */
class Contact_BK {
	/**
	 * Contact id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Contact WP user ID.
	 *
	 * @since 1.2.1
	 * @var null
	 */
	public $user_id = null;

	/**
	 * Contact name.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $name = '';

	/**
	 * Contact company.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $company = '';

	/**
	 * Contact email.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $email = '';

	/**
	 * Contact phone.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $phone = '';

	/**
	 * Contact website.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $website = '';

	/**
	 * Contact birthdate
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $birth_date = '';

	/**
	 * Contact vat number.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $vat_number = '';

	/**
	 * Contact vat number.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $street = '';

	/**
	 * Contact street.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $city = '';

	/**
	 * Contact city.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $state = '';

	/**
	 * Contact postcode.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $postcode = '';

	/**
	 * Contact country.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $country = '';

	/**
	 * Contact currency code.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $currency_code = '';

	/**
	 * Contact type.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $type = '';


	/**
	 * Contact thumbnail id.
	 *
	 * @since 1.2.1
	 * @var null
	 */
	public $thumbnail_id = null;

	/**
	 * Contact status
	 *
	 * @since 1.2.1
	 * @var bool
	 */
	public $enabled = true;

	/**
	 * Contact creator user id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $creator_id = 0;

	/**
	 * Contact created date.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $date_created = '0000-00-00 00:00:00';

	/**
	 * Stores the contact object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $filter;



	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Contact field to check if set.
	 *
	 * @return bool Whether the given Contact field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		if ( isset( $this->data->$key ) ) {
			return true;
		}

		return metadata_exists( 'contact', $this->id, $key );
	}

	/**
	 * Magic method for setting contact fields.
	 *
	 * This method does not update custom fields in the database. It only stores
	 * the value on the WP_User instance.
	 *
	 * @param string $key Contact key.
	 * @param mixed $value Contact value.
	 *
	 * @since 1.2.1
	 */
	public function __set( $key, $value ) {
		if ( is_callable( array( $this, 'set_' . $key ) ) ) {
			$this->$key( $value );
		} elseif ( isset( $this->data->$key ) ) {
			$this->data->$key = $value;
		} else {
			$this->update_meta( $key, $value );
		}
	}

	/**
	 * Magic method for accessing custom fields.
	 *
	 * @param string $key User field to retrieve.
	 *
	 * @return mixed Value of the given Contact field (if set).
	 * @since 1.2.1
	 */
	public function __get( $key ) {

		if ( is_callable( array( $this, 'get_' . $key ) ) ) {
			$value = $this->$key();
		} elseif ( isset( $this->data->$key ) ) {
			$value = $this->data->$key;
		} else {
			$value = $this->get_meta( $key, true );
		}

		return $value;
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key Contact key to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		if ( isset( $this->data->$key ) ) {
			unset( $this->data->$key );
		}
	}

	/**
	 * Get meta data.
	 *
	 * @param string $meta_key Meta key
	 * @param boolean $single Single
	 *
	 * @return array|false|mixed
	 * @since 1.2.1
	 */
	protected function get_meta( string $meta_key = '', bool $single = true ) {
		return get_metadata( 'contact', $this->id, $meta_key, $single );
	}

	/**
	 * Update meta value.
	 *
	 * @param string $meta_key Meta key
	 * @param string $meta_value Meta value
	 * @param string $prev_value Previous value
	 *
	 * @return bool|int
	 * @since 1.2.1
	 */
	protected function update_meta( string $meta_key, string $meta_value, string $prev_value = '' ) {
		return update_metadata( 'contact', $this->id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * Consults the users and contact_meta tables.
	 *
	 * @param string $key Property
	 *
	 * @return bool
	 * @since 1.2.1
	 */
	public function has_prop( string $key ) {
		return $this->__isset( $key );
	}

	/**
	 * Determine whether the contact exists in the database.
	 *
	 * @return bool True if contact exists in the database, false if not.
	 * @since 1.2.1
	 */
	public function exists() {
		return ! empty( $this->id );
	}

	/**
	 * Return an array representation.
	 *
	 * @return array Array representation.
	 * @since 1.2.1
	 */
	public function to_array() {
		return get_object_vars( $this->data );
	}

	/**
	 * Get contact's country.
	 *
	 * @param string $context Context
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_country_nicename( $context = 'edit' ) {
		$countries = eaccounting_get_countries();

		return isset( $countries[ $this->country ] ) ? $countries[ $this->country ] : $this->country;
	}

	/**
	 * Get total paid by a customer.
	 *
	 * @return float|int|string
	 * @since 1.1.0
	 */
	public function get_customer_calculated_total_paid() {
		global $wpdb;
		$total = wp_cache_get( 'total_paid_' . $this->id, 'ea_contacts' );
		if ( false === $total ) {
			$total        = 0;
			$transactions = $wpdb->get_results( $wpdb->prepare( "SELECT amount, currency_code, currency_rate FROM {$wpdb->prefix}ea_transactions WHERE type='income' AND contact_id=%d", $this->id ) );
			foreach ( $transactions as $transaction ) {
				$total += eaccounting_price_to_default( $transaction->amount, $transaction->currency_code, $transaction->currency_rate );
			}
			wp_cache_set( 'total_paid_' . $this->id, $total, 'ea_contacts' );
		}

		return $total;
	}

	/**
	 * Get total paid by a customer.
	 *
	 * @return float|int|string
	 * @since 1.1.0
	 */
	public function get_customer_calculated_total_due() {
		global $wpdb;
		$total = wp_cache_get( 'total_due_' . $this->id, 'ea_contacts' );
		if ( false === $total ) {
			$invoices = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT id, total amount, currency_code, currency_rate  FROM   {$wpdb->prefix}ea_documents
					   WHERE  status NOT IN ( 'draft', 'cancelled', 'paid' )
					   AND type = 'invoice' AND contact_id=%d",
					$this->id
				)
			);
			$total    = 0;
			foreach ( $invoices as $invoice ) {
				$total += eaccounting_price_to_default( $invoice->amount, $invoice->currency_code, $invoice->currency_rate );
			}
			if ( ! empty( $total ) ) {
				$invoice_ids = implode( ',', wp_parse_id_list( wp_list_pluck( $invoices, 'id' ) ) );
				$revenues    = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT Sum(amount) amount, currency_code, currency_rate
		  			   FROM   {$wpdb->prefix}ea_transactions
		               WHERE  type = %s AND document_id IN ($invoice_ids)
		  			   GROUP  BY currency_code,currency_rate",
						'income'
					)
				);

				foreach ( $revenues as $revenue ) {
					$total -= eaccounting_price_to_default( $revenue->amount, $revenue->currency_code, $revenue->currency_rate );
				}
			}
			wp_cache_set( 'total_due' . $this->id, $total, 'ea_contacts' );
		}

		return $total;
	}

	/**
	 * Get total paid by a vendor.
	 *
	 * @return float|int|string
	 * @since 1.1.0
	 */
	public function get_vendor_calculated_total_paid() {
		global $wpdb;
		$total = wp_cache_get( 'vendor_total_total_paid_' . $this->id, 'ea_contacts' );
		if ( false === $total ) {
			$total        = 0;
			$transactions = $wpdb->get_results( $wpdb->prepare( "SELECT amount, currency_code, currency_rate FROM {$wpdb->prefix}ea_transactions WHERE type='expense' AND contact_id=%d", $this->id ) );
			foreach ( $transactions as $transaction ) {
				$total += eaccounting_price_to_default( $transaction->amount, $transaction->currency_code, $transaction->currency_rate );
			}
			wp_cache_set( 'vendor_total_total_paid_' . $this->id, $total, 'ea_contacts' );
		}

		return $total;
	}

	/**
	 * Get total due by a vendor.
	 *
	 * @return float|int|string
	 * @since 1.1.0
	 */
	public function get_calculated_total_due() {
		global $wpdb;
		$total = wp_cache_get( 'vendor_total_total_due_' . $this->id, 'ea_contacts' );
		if ( false === $total ) {
			$bills = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT id, total amount, currency_code, currency_rate  FROM   {$wpdb->prefix}ea_documents
					   WHERE  status NOT IN ( 'draft', 'cancelled', 'paid' )
					   AND type = 'bill' AND contact_id=%d",
					$this->id
				)
			);

			$total = 0;
			foreach ( $bills as $bill ) {
				$total += eaccounting_price_to_default( $bill->amount, $bill->currency_code, $bill->currency_rate );
			}

			if ( ! empty( $total ) ) {
				$bill_ids = implode( ',', wp_parse_id_list( wp_list_pluck( $bills, 'id' ) ) );
				$revenues = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT Sum(amount) amount, currency_code, currency_rate
		  			   FROM   {$wpdb->prefix}ea_transactions
		               WHERE  type = %s AND document_id IN ($bill_ids)
		  			   GROUP  BY currency_code,currency_rate",
						'expense'
					)
				);

				foreach ( $revenues as $revenue ) {
					$total -= eaccounting_price_to_default( $revenue->amount, $revenue->currency_code, $revenue->currency_rate );
				}
			}
			wp_cache_set( 'vendor_total_total_due_' . $this->id, $total, 'ea_contacts' );
		}

		return $total;
	}
}
