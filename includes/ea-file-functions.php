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
			$upload_dir               = eaccounting_get_upload_dir();
			$file_url                 = trailingslashit( $upload_dir['baseurl'] ) . ltrim( $upload_dir['subdir'], '/' ) . '/' . $file_name;
			$uploaded_file->name      = $file_name;
			$uploaded_file->path      = $upload_dir['subdir'];
			$uploaded_file->mime_type = $file['type'];
			$uploaded_file->size      = $file['size'];
			$uploaded_file->url       = $file_url;
			$uploaded_file->extension = substr( strrchr( $full_path, '.' ), 1 );
		}
	}

	return $uploaded_file;
}
