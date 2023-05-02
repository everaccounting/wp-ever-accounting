<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Media.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Media extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_media';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'media';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_media';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'name'        => '',
		'path'        => '',
		'type'        => '',
		'size'        => '',
		'description' => '',
		'ext'         => '',
		'mime'        => '',
		'creator_id'  => null,
		'updated_at'  => null,
		'created_at'  => null,
	);

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data.
	|
	*/
	/**
	 * Return the name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set name.
	 *
	 * @param string $name Media name.
	 *
	 * @since 1.1.0
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', sanitize_file_name( $name ) );
	}

	/**
	 * Return the path.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_path( $context = 'edit' ) {
		return $this->get_prop( 'path', $context );
	}

	/**
	 * Set path.
	 *
	 * @param string $path Media path.
	 *
	 * @since 1.1.0
	 */
	public function set_path( $path ) {
		$this->set_prop( 'path', sanitize_text_field( $path ) );
	}

	/**
	 * Return the type.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Set type.
	 *
	 * @param string $type Media type.
	 *
	 * @since 1.1.0
	 */
	public function set_type( $type ) {
		$this->set_prop( 'type', sanitize_text_field( $type ) );
	}

	/**
	 * Return the size.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_size( $context = 'edit' ) {
		return $this->get_prop( 'size', $context );
	}

	/**
	 * Set size.
	 *
	 * @param string $size Media size.
	 *
	 * @since 1.1.0
	 */
	public function set_size( $size ) {
		$this->set_prop( 'size', sanitize_text_field( $size ) );
	}

	/**
	 * Return the description.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Set description.
	 *
	 * @param string $description Media description.
	 *
	 * @since 1.1.0
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', sanitize_text_field( $description ) );
	}

	/**
	 * Return the ext.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_ext( $context = 'edit' ) {
		return $this->get_prop( 'ext', $context );
	}

	/**
	 * Set ext.
	 *
	 * @param string $ext Media ext.
	 *
	 * @since 1.1.0
	 */
	public function set_ext( $ext ) {
		$this->set_prop( 'ext', sanitize_text_field( $ext ) );
	}

	/**
	 * Return the mime.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_mime( $context = 'edit' ) {
		return $this->get_prop( 'mime', $context );
	}

	/**
	 * Set mime.
	 *
	 * @param string $mime Media mime.
	 *
	 * @since 1.1.0
	 */
	public function set_mime( $mime ) {
		$this->set_prop( 'mime', sanitize_text_field( $mime ) );
	}

	/**
	 * Get the creator id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return int
	 */
	public function get_creator_id( $context = 'edit' ) {
		return $this->get_prop( 'creator_id', $context );
	}

	/**
	 * Set the creator id.
	 *
	 * @param int $creator_id creator id.
	 */
	public function set_creator_id( $creator_id ) {
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}

	/**
	 * Get the date updated.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_updated_at( $context = 'edit' ) {
		return $this->get_prop( 'updated_at', $context );
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $updated_at date updated.
	 */
	public function set_updated_at( $updated_at ) {
		$this->set_date_prop( 'updated_at', $updated_at );
	}

	/**
	 * Get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_created_at( $context = 'edit' ) {
		return $this->get_prop( 'created_at', $context );
	}

	/**
	 * Set the date created.
	 *
	 * @param string $created_at date created.
	 */
	public function set_created_at( $created_at ) {
		$this->set_date_prop( 'created_at', $created_at );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		// Required fields check.
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'missing_required', __( 'Media name is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_path() ) ) {
			return new \WP_Error( 'missing_required', __( 'Media path is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_type() ) ) {
			return new \WP_Error( 'missing_required', __( 'Media type is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_size() ) ) {
			return new \WP_Error( 'missing_required', __( 'Media size is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_ext() ) ) {
			return new \WP_Error( 'missing_required', __( 'Media ext is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_mime() ) ) {
			return new \WP_Error( 'missing_required', __( 'Media mime is required.', 'wp-ever-accounting' ) );
		}

		// Creator ID.
		if ( empty( $this->get_creator_id() ) && ! $this->exists() && is_user_logged_in() ) {
			$this->set_creator_id( get_current_user_id() );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_updated_at( current_time( 'mysql' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_created_at() ) ) {
			$this->set_created_at( current_time( 'mysql' ) );
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/
	/**
	 * Get the media url.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_url() {
		return wp_get_attachment_url( $this->get_id() );
	}

	/**
	 * Output the media
	 *
	 * @param int $height Height of the media.
	 * @param int $width Width of the media.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function preview( $height = 100, $width = 100 ) {
		$media_url = $this->get_url();
		$media_url = apply_filters( 'ever_media_preview_url', $media_url, $this->get_id() );
		$media_url = apply_filters( 'ever_media_preview_url_' . $this->get_id(), $media_url, $this->get_id() );

		if ( ! $media_url ) {
			return;
		}

		$media_url = add_query_arg(
			array(
				'height' => $height,
				'width'  => $width,
			),
			$media_url
		);

		return '<img src="' . esc_url( $media_url ) . '" alt="' . esc_attr( $this->get_name() ) . '" />';

	}

	/**
	 * Upload media.
	 *
	 * @param array $file Temporary file.
	 *
	 * @since 1.0.0
	 * @return static|\WP_Error Media object on success, WP_Error on failure.
	 */
	public static function upload( $file ) {
		// Check file.
		if ( empty( $file ) ) {
			return new \WP_Error( 'missing_file', __( 'No file was uploaded.', 'wp-ever-accounting' ) );
		}

		$filename = wp_basename( $file['name'] );

		// Check file type.
		$allowed_types = array(
			'image/jpeg',
			'image/png',
			'image/gif',
			'application/pdf',
			'application/msword',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.ms-powerpoint',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		);

		if ( ! in_array( $file['type'], $allowed_types, true ) ) {
			return new \WP_Error( 'invalid_file_type', __( 'Invalid file type.', 'wp-ever-accounting' ) );
		}

		// Check file size.
		$max_size = wp_max_upload_size();

		if ( $file['size'] > $max_size ) {
			return new \WP_Error( 'file_too_large', __( 'File is too large.', 'wp-ever-accounting' ) );
		}

		// Upload file.
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}
		$data   = $wp_filesystem->get_contents( $file );
		$upload = wp_upload_bits( $filename, null, $data );

		if ( ! empty( $upload['error'] ) ) {
			return new \WP_Error( 'upload_failed', $upload['error'] );
		}

		// Get file info.
		$ext  = pathinfo( $upload['file'], PATHINFO_EXTENSION );
		$mime = wp_check_filetype( $upload['file'] );

		// Create media object.
		$media = static::insert(
			array(
				'name'       => sanitize_file_name( $file['name'] ),
				'path'       => $upload['file'],
				'type'       => $mime['type'],
				'size'       => $file['size'],
				'ext'        => $ext,
				'mime'       => $mime['type'],
				'creator_id' => get_current_user_id(),
			)
		);

		return $media;
	}
}
