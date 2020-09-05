<?php
/**
 * EverAccounting File Functions.
 *
 * File related functions.
 *
 * @since   1.0.2
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit();

/**
 * Set upload directory for Accounting
 *
 * @since 1.0.2
 * @return array
 */
function eaccounting_get_upload_dir() {
	$upload            = wp_upload_dir();
	$upload['basedir'] = $upload['basedir'] . '/eaccounting';
	$upload['path']    = $upload['basedir'] . $upload['subdir'];
	$upload['baseurl'] = $upload['baseurl'] . '/eaccounting';
	$upload['url']     = $upload['baseurl'] . $upload['subdir'];

	return $upload;
}

/**
 * Scan folders
 *
 * @since 1.0.2
 *
 * @param array  $return
 *
 * @param string $path
 *
 * @return array
 */
function eaccounting_scan_folders( $path = '', $return = array() ) {
	$path  = $path == '' ? dirname( __FILE__ ) : $path;
	$lists = @scandir( $path );

	if ( ! empty( $lists ) ) {
		foreach ( $lists as $f ) {
			if ( is_dir( $path . DIRECTORY_SEPARATOR . $f ) && $f != "." && $f != ".." ) {
				if ( ! in_array( $path . DIRECTORY_SEPARATOR . $f, $return ) ) {
					$return[] = trailingslashit( $path . DIRECTORY_SEPARATOR . $f );
				}

				eaccounting_scan_folders( $path . DIRECTORY_SEPARATOR . $f, $return );
			}
		}
	}

	return $return;
}

/**
 * Protect accounting files
 *
 * @since 1.0.2
 *
 * @param bool $force
 *
 */
function eaccounting_protect_files( $force = false ) {

	if ( false === get_transient( 'eaccounting_check_protection_files' ) || $force ) {
		$upload_dir = eaccounting_get_upload_dir();
		if ( ! is_dir( $upload_dir['path'] ) ) {
			wp_mkdir_p( $upload_dir['path'] );
		}

		$base_dir = $upload_dir['basedir'];

		$htaccess = trailingslashit( $base_dir ) . '.htaccess';
		if ( ! file_exists( $htaccess ) ) {
			$rule = "order deny,allow\n";
			$rule .= "deny from all\n";
			$rule .= "allow from 127.0.0.1\n";
			@file_put_contents( $htaccess, $rule );
		}

		// Top level blank index.php
		if ( ! file_exists( $base_dir . '/index.php' ) && wp_is_writable( $base_dir ) ) {
			@file_put_contents( $base_dir . '/index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
		}

		$folders = eaccounting_scan_folders( $base_dir );
		foreach ( $folders as $folder ) {
			// Create index.php, if it doesn't exist
			if ( ! file_exists( $folder . 'index.php' ) && wp_is_writable( $folder ) ) {
				@file_put_contents( $folder . 'index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
			}
		}

		// Check for the files once per day
		set_transient( 'eaccounting_check_protection_files', true, 3600 * 24 );
	}
}

/**
 * Prepares files for upload by standardizing them into an array. This adds support for multiple file upload fields.
 *
 * @since 1.0.2
 *
 * @param array $file_data
 *
 * @return array
 */
function eaccounting_prepare_uploaded_files( $file_data ) {
	$files_to_upload = [];

	if ( is_array( $file_data['name'] ) ) {
		foreach ( $file_data['name'] as $file_data_key => $file_data_value ) {
			if ( $file_data['name'][ $file_data_key ] ) {
				$type              = wp_check_filetype( $file_data['name'][ $file_data_key ] ); // Map mime type to one WordPress recognises.
				$files_to_upload[] = [
					'name'     => $file_data['name'][ $file_data_key ],
					'type'     => $type['type'],
					'tmp_name' => $file_data['tmp_name'][ $file_data_key ],
					'error'    => $file_data['error'][ $file_data_key ],
					'size'     => $file_data['size'][ $file_data_key ],
				];
			}
		}
	} else {
		$type              = wp_check_filetype( $file_data['name'] ); // Map mime type to one WordPress recognises.
		$file_data['type'] = $type['type'];
		$files_to_upload[] = $file_data;
	}

	return apply_filters( 'eaccounting_prepare_uploaded_files', $files_to_upload );
}

/**
 * Returns mime types specifically for eaccounting
 *
 * @since 1.0.2
 *
 * @param string $field Field used.
 *
 * @return  array  Array of allowed mime types
 */
function eaccounting_get_allowed_mime_types() {
	$allowed_mime_types = [
		'jpg|jpeg|jpe' => 'image/jpeg',
		'gif'          => 'image/gif',
		'png'          => 'image/png',
		'pdf'          => 'application/pdf',
		'doc'          => 'application/msword',
		'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	];

	return apply_filters( 'eaccounting_mime_types', $allowed_mime_types );
}


/**
 * Uploads a file using WordPress file API.
 *
 * @since 1.0.2
 *
 * @param string|array|object $args Optional arguments.
 *
 * @param array|WP_Error      $file Array of $_FILE data to upload.
 *
 * @return stdClass|WP_Error Object containing file information, or error.
 */
function eaccounting_upload_file( $file, $args = [] ) {
	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/media.php';

	$args = wp_parse_args( $args, [ 'allowed_mime_types' => '' ] );

	$uploaded_file = new stdClass();
	if ( '' === $args['allowed_mime_types'] ) {
		$allowed_mime_types = eaccounting_get_allowed_mime_types();
	} else {
		$allowed_mime_types = $args['allowed_mime_types'];
	}

	$file = apply_filters( 'eaccounting_upload_file_pre_upload', $file, $args, $allowed_mime_types );

	if ( is_wp_error( $file ) ) {
		return $file;
	}

	if ( ! in_array( $file['type'], $allowed_mime_types, true ) ) {
		// Replace pipe separating similar extensions (e.g. jpeg|jpg) to comma to match the list separator.
		$allowed_file_extensions = implode( ', ', str_replace( '|', ', ', array_keys( $allowed_mime_types ) ) );

		return new WP_Error( 'upload', sprintf( __( 'Uploaded files need to be one of the following file types: %s', 'wp-ever-accounting' ), $allowed_file_extensions ) );
	} else {
		$upload_dir = eaccounting_get_upload_dir();

		//clean name
		$original_name = str_replace( '\\', '/', $file['name'] );
		$pos           = strrpos( $original_name, '/' );
		$original_name = false === $pos ? $original_name : substr( $original_name, $pos + 1 );
		$original_name = strlen( $original_name ) > 180 ? substr( $original_name, - 1, 180 ) : $original_name;
		$file_name     = remove_accents( strtolower( $original_name ) );

		//set folder if not exist
		if ( ! is_dir( $upload_dir['path'] ) ) {
			wp_mkdir_p( $upload_dir['path'] );
		}

		//if exist another change the name
		if ( file_exists( trailingslashit( $upload_dir['path'] ) . $file_name ) ) {
			$file_name = substr( md5( time() ), 0, 3 ) . '-' . $file_name;
		}

		$full_path = trailingslashit( $upload_dir['path'] ) . $file_name;
		$uploaded  = move_uploaded_file( $file['tmp_name'], $full_path );

		if ( ! $uploaded ) {
			return new WP_Error( 'upload', __( 'Could not upload file', 'wp-ever-accounting' ) );
		} else {
			$uploaded_file->name      = $file_name;
			$uploaded_file->path      = $upload_dir['subdir'];
			$uploaded_file->mime_type = $file['type'];
			$uploaded_file->size      = $file['size'];
			$uploaded_file->extension = substr( strrchr( $full_path, '.' ), 1 );
		}
	}

	return $uploaded_file;
}

/**
 * Insert file into database
 *
 * @since 1.0.2
 *
 * @param $args
 *
 * @return int|WP_Error|null
 */
function eaccounting_insert_file( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;
	$args   = (array) apply_filters( 'eaccounting_create_file', $args );

	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = (int) $args['id'];
		$update      = true;
		$item_before = (array) eaccounting_get_file( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to  update', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}

	$data = array(
		'id'         => empty( $args['id'] ) ? null : absint( $args['id'] ),
		'name'       => ! isset( $args['name'] ) ? '' : remove_accents( $args['name'] ),
		'path'       => ! isset( $args['path'] ) ? '' : sanitize_text_field( $args['path'] ),
		'extension'  => ! isset( $args['extension'] ) ? '' : sanitize_text_field( $args['extension'] ),
		'mime_type'  => ! isset( $args['mime_type'] ) ? '' : sanitize_text_field( $args['mime_type'] ),
		'size'       => ! isset( $args['size'] ) ? '' : intval( $args['size'] ),
		'creator_id' => empty( $args['creator_id'] ) ? eaccounting_get_current_user_id() : $args['creator_id'],
		'created_at' => empty( $args['created_at'] ) ? current_time( 'Y-m-d H:i:s' ) : $args['created_at'],
	);

	if ( empty( $data['name'] ) ) {
		return new WP_Error( 'empty_content', __( 'Name is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['path'] ) ) {
		return new WP_Error( 'empty_content', __( 'Path is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['extension'] ) ) {
		return new WP_Error( 'empty_content', __( 'Extension is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['mime_type'] ) ) {
		return new WP_Error( 'empty_content', __( 'Mite type is required', 'wp-ever-accounting' ) );
	}

	$where = array( 'id' => $id );
	$data  = wp_unslash( $data );
	if ( $update ) {
		do_action( 'eaccounting_pre_file_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_files, $data, $where ) ) {
			return new WP_Error( 'db_update_error', __( 'Could not update file in the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		do_action( 'eaccounting_file_update', $id, $data, $item_before );
	} else {
		do_action( 'eaccounting_pre_file_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_files, $data ) ) {

			return new WP_Error( 'db_insert_error', __( 'Could not insert file into the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'eaccounting_file_insert', $id, $data );
	}

	return $id;

}

/**
 * Get Single file from id
 *
 * @since 1.0.2
 *
 * @param $id
 *
 * @return array|object|void|null
 */
function eaccounting_get_file( $id ) {
	global $wpdb;

	return $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->ea_files} where id=%s", $id ) );
}

/**
 * Delete file
 *
 * @since 1.0.2
 *
 * @param $id
 *
 * @return bool
 */
function eaccounting_delete_file( $id ) {
	global $wpdb;
	$id   = absint( $id );
	$file = eaccounting_get_file( $id );
	if ( is_null( $file ) ) {
		return false;
	}
	do_action( 'eaccounting_pre_file_delete', $id, $file );
	if ( false == $wpdb->delete( $wpdb->ea_files, array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'eaccounting_file_delete', $id, $file );
	$path = eaccounting_get_file_path( $file );
	@unlink( $path );

	return true;
}

/**
 * Get files
 *
 * @since 1.0.2
 *
 * @param bool $count
 *
 * @param      $args
 *
 * @return array|object|string|null
 */
function eaccounting_get_files( $args, $count = false ) {
	global $wpdb;
	$query_fields  = '';
	$query_from    = '';
	$query_where   = '';
	$query_orderby = '';
	$query_limit   = '';

	$default = array(
		'include'        => array(),
		'exclude'        => array(),
		'search'         => '',
		'orderby'        => 'id',
		'order'          => 'DESC',
		'fields'         => 'all',
		'search_columns' => array( 'name', 'path' ),
		'per_page'       => 20,
		'page'           => 1,
		'offset'         => 0,
	);

	$args        = wp_parse_args( $args, $default );
	$query_from  = "FROM $wpdb->ea_files";
	$query_where = 'WHERE 1=1';

	//fields
	if ( is_array( $args['fields'] ) ) {
		$args['fields'] = array_unique( $args['fields'] );

		$query_fields = array();
		foreach ( $args['fields'] as $field ) {
			$field          = 'id' === $field ? 'id' : sanitize_key( $field );
			$query_fields[] = "$wpdb->ea_files.$field";
		}
		$query_fields = implode( ',', $query_fields );
	} elseif ( 'all' == $args['fields'] ) {
		$query_fields = "$wpdb->ea_files.*";
	} else {
		$query_fields = "$wpdb->ea_files.id";
	}

	//include
	$include = false;
	if ( ! empty( $args['include'] ) ) {
		$include = wp_parse_id_list( $args['include'] );
	}

	if ( ! empty( $include ) ) {
		// Sanitized earlier.
		$ids         = implode( ',', $include );
		$query_where .= " AND $wpdb->ea_files.id IN ($ids)";
	} elseif ( ! empty( $args['exclude'] ) ) {
		$ids         = implode( ',', wp_parse_id_list( $args['exclude'] ) );
		$query_where .= " AND $wpdb->ea_files.id NOT IN ($ids)";
	}

	//search
	$search = '';
	if ( isset( $args['search'] ) ) {
		$search = trim( $args['search'] );
	}
	if ( $search ) {
		$searches = array();
		$cols     = array_map( 'sanitize_key', $args['search_columns'] );
		$like     = '%' . $wpdb->esc_like( $search ) . '%';
		foreach ( $cols as $col ) {
			$searches[] = $wpdb->prepare( "$col LIKE %s", $like );
		}

		$query_where .= ' AND (' . implode( ' OR ', $searches ) . ')';
	}


	//ordering
	$order         = isset( $args['order'] ) ? esc_sql( strtoupper( $args['order'] ) ) : 'ASC';
	$order_by      = esc_sql( $args['orderby'] );
	$query_orderby = sprintf( " ORDER BY %s %s ", $order_by, $order );

	// limit
	if ( isset( $args['per_page'] ) && $args['per_page'] > 0 ) {
		if ( $args['offset'] ) {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $args['offset'], $args['per_page'] );
		} else {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $args['per_page'] * ( $args['page'] - 1 ), $args['per_page'] );
		}
	}

	if ( $count ) {
		return $wpdb->get_var( "SELECT count($wpdb->ea_files.id) $query_from $query_where" );
	}


	$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( is_array( $args['fields'] ) || 'all' == $args['fields'] ) {
		return $wpdb->get_results( $request );
	}

	return $wpdb->get_col( $request );
}

/**
 * Get the file url
 *
 * @since 1.0.2
 *
 * @param $file
 *
 * @return string|WP_Error
 */
function eaccounting_get_file_url( $file ) {
	if ( is_numeric( $file ) ) {
		$file = eaccounting_get_file( $file );
	}

	if ( empty( $file ) || ! is_object( $file ) || ! isset( $file->mime_type ) ) {
		return new WP_Error( 'invalid', __( 'Invalid file, could not retrieve url', 'wp-ever-accounting' ) );
	}

	$upload_dir = eaccounting_get_upload_dir();
	$file_url   = trailingslashit( $upload_dir['baseurl'] ) . ltrim( $file->path, '/' ) . '/' . $file->name;

	return esc_url( $file_url );
}


/**
 * Get file path
 *
 * @since 1.0.2
 *
 * @param $file
 *
 * @return string|WP_Error
 */
function eaccounting_get_file_path( $file ) {
	if ( is_numeric( $file ) ) {
		$file = eaccounting_get_file( $file );
	}

	if ( empty( $file ) || ! is_object( $file ) || ! isset( $file->mime_type ) ) {
		return new WP_Error( 'invalid', __( 'Invalid file, could not retrieve url', 'wp-ever-accounting' ) );
	}

	$upload_dir = eaccounting_get_upload_dir();
	$file_path  = trailingslashit( $upload_dir['basedir'] ) . ltrim( $file->path, '/' ) . '/' . $file->name;

	return $file_path;
}
