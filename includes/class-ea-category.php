<?php
/**
 * Handle the category object.
 *
 * @package     EverAccounting
 * @class       EAccounting_Category
 * @version     1.0.2
 *
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Base_Object;

defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Category
 *
 * @since 1.0.2
 */
class Category extends Base_Object {
	/**
	 * Category Data array.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $data = array(
		'name'         => '',
		'type'         => '',
		'color'        => '',
		'enabled'      => 1,
		'company_id'   => null,
		'date_created' => null,
	);

	/**
	 * Get the category if ID is passed, otherwise the category is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_category function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param int|object|Category $data object to read.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} else {
			$this->set_id( 0 );
		}

		if ( $this->get_id() > 0 ) {
			$this->read( $this->get_id() );
		}
	}


	/**
	 * Load category from database.
	 *
	 * @param int $id
	 *
	 * @throws \Exception
	 * @since 1.0.2
	 */
	public function read( $id ) {
		$this->set_defaults();
		global $wpdb;

		// Get from cache if available.
		$item = 0 < $this->get_id() ? wp_cache_get( 'category-item-' . $this->get_id(), 'categories' ) : false;
		if ( false === $item ) {
			$item = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_categories WHERE id = %d;", $this->get_id() )
			);

			if ( 0 < $item->id ) {
				wp_cache_set( 'category-item-' . $item->id, $item, 'categories' );
			}
		}

		if ( ! $item || ! $item->id ) {
			throw new \Exception( __( 'Invalid category.', 'wp-ever-accounting' ) );
		}

		// Gets extra data associated with the order if needed.
		foreach ( $item as $key => $value ) {
			$function = 'set_' . $key;
			if ( is_callable( array( $this, $function ) ) ) {
				$this->{$function}( $value );
			}
		}

		$this->set_object_read( true );
	}

	/**
	 * Validate the properties before saving the object
	 * in the database.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function validate_props() {
		global $wpdb;

		if ( ! $this->get_date_created( 'edit' ) ) {
			$this->set_date_created( time() );
		}

		if ( ! $this->get_company_id( 'edit' ) ) {
			$this->set_company_id( 1 );
		}

		if ( ! $this->get_prop( 'creator_id' ) ) {
			$this->set_prop( 'creator_id', eaccounting_get_current_user_id() );
		}

		if ( ! $this->get_color( 'edit' ) ) {
			$this->set_color( eaccounting_get_random_color() );
		}

		if ( empty( $this->get_name( 'edit' ) ) ) {
			throw new \Exception( __( 'Category name is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_type( 'edit' ) ) ) {
			throw new \Exception( __( 'Category type is required', 'wp-ever-accounting' ) );
		}


		if ( $existing_id = $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_categories where name=%s AND type=%s", $this->get_name( 'edit' ), $this->get_type( 'edit' ) ) ) ) {
			if ( ! empty( $existing_id ) && $existing_id != $this->get_id() ) {
				throw new \Exception( __( 'Duplicate category name.', 'wp-ever-accounting' ) );
			}
		}

	}

	/**
	 * Create a new account in the database.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function create() {
		$this->validate_props();
		global $wpdb;
		$account_data = array(
			'name'         => $this->get_name( 'edit' ),
			'type'         => $this->get_type( 'edit' ),
			'color'        => $this->get_color( 'edit' ),
			'company_id'   => $this->get_company_id( 'edit' ),
			'date_created' => $this->get_date_created( 'edit' )->get_mysql_date(),
		);

		do_action( 'eaccounting_pre_insert_category', $this->get_id(), $this );

		$data = wp_unslash( apply_filters( 'eaccounting_new_category  _data', $account_data ) );
		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_categories', $data ) ) {
			throw new \Exception( $wpdb->last_error );
		}

		do_action( 'eaccounting_insert_category', $this->get_id(), $this );

		$this->set_id( $wpdb->insert_id );
		$this->apply_changes();
		$this->set_object_read( true );
	}

	/**
	 * Update the category in the database.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 *
	 */
	public function update() {
		global $wpdb;

		$this->validate_props();
		$changes = $this->get_changes();
		if ( ! empty( $changes ) ) {
			do_action( 'eaccounting_pre_update_category', $this->get_id(), $changes );

			try {
				$wpdb->update( $wpdb->prefix . 'ea_categories', $changes, array( 'id' => $this->get_id() ) );
			} catch ( \Exception $e ) {
				throw new \Exception( __( 'Could not update category.', 'wp-ever-accounting' ) );
			}

			do_action( 'eaccounting_update_category', $this->get_id(), $changes, $this->data );

			$this->apply_changes();
			$this->set_object_read( true );
			wp_cache_delete( 'category-item-' . $this->get_id(), 'categories' );
		}
	}

	/**
	 * Conditionally save category in the database
	 * if exist then update otherwise create.
	 *
	 * @return int|mixed
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function save() {
		if ( $this->get_id() ) {
			$this->update();
		} else {
			$this->create();
		}

		return $this->get_id();
	}


	/**
	 * Remove the category from the database.
	 *
	 * @param array $args
	 *
	 * @since 1.0.2
	 */
	public function delete( $args = array() ) {
		if ( $this->get_id() ) {
			global $wpdb;
			do_action( 'eaccounting_pre_delete_category', $this->get_id() );
			$wpdb->delete( $wpdb->prefix . 'ea_categories', array( 'id' => $this->get_id() ) );
			do_action( 'eaccounting_delete_category', $this->get_id() );
			$this->set_id( 0 );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get category name.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get the category type.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_type( $context = 'view' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Get the category color.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_color( $context = 'view' ) {
		return $this->get_prop( 'color', $context );
	}


	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set the category name.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_name( $value ) {
		$this->set_prop( 'name', eaccounting_clean( $value ) );
	}

	/**
	 * Set the category type.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_type( $value ) {
		if ( array_key_exists( $value, eaccounting_get_category_types() ) ) {
			$this->set_prop( 'type', eaccounting_clean( $value ) );
		}
	}

	/**
	 * Set the category color.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_color( $value ) {
		$this->set_prop( 'color', eaccounting_clean( $value ) );
	}

}
