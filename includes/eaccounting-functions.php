<?php
defined( 'ABSPATH' ) || exit();

function eaccounting_upload_file( $file, $args = [] ) {
	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/media.php';

	$args = wp_parse_args(
		$args,
		[
			'file_key'           => '',
			'file_label'         => '',
			'allowed_mime_types' => '',
		]
	);

	$uploading_file_key = $args['file_key'];
	$uploaded_file      = new stdClass();
	if ( '' === $args['allowed_mime_types'] ) {
		$allowed_mime_types = eaccounting_get_allowed_mime_types( $uploaded_file );
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

		if ( $args['file_label'] ) {
			// translators: %1$s is the file field label; %2$s is the file type; %3$s is the list of allowed file types.
			return new WP_Error( 'upload', sprintf( __( '"%1$s" (filetype %2$s) needs to be one of the following file types: %3$s', 'wp-job-manager' ), $args['file_label'], $file['type'], $allowed_file_extensions ) );
		} else {
			// translators: %s is the list of allowed file types.
			return new WP_Error( 'upload', sprintf( __( 'Uploaded files need to be one of the following file types: %s', 'wp-job-manager' ), $allowed_file_extensions ) );
		}
	} else {
		$upload = wp_handle_upload( $file, apply_filters( 'eaccounting_upload_wp_handle_upload_overrides', [ 'test_form' => false ] ) );
		if ( ! empty( $upload['error'] ) ) {
			return new WP_Error( 'upload', $upload['error'] );
		} else {
			$uploaded_file->url       = $upload['url'];
			$uploaded_file->file      = $upload['file'];
			$uploaded_file->name      = basename( $upload['file'] );
			$uploaded_file->type      = $upload['type'];
			$uploaded_file->size      = $file['size'];
			$uploaded_file->extension = substr( strrchr( $uploaded_file->name, '.' ), 1 );
		}
	}


	return $uploaded_file;
}


/**
 * Returns mime types specifically for WPJM.
 *
 * @since 1.25.1
 * @param   string $field Field used.
 * @return  array  Array of allowed mime types
 */
function eaccounting_get_allowed_mime_types( $field = '' ) {
	if ( 'company_logo' === $field ) {
		$allowed_mime_types = [
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
		];
	} else {
		$allowed_mime_types = [
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'pdf'          => 'application/pdf',
			'doc'          => 'application/msword',
			'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		];
	}

	return apply_filters( 'eaccounting_mime_types', $allowed_mime_types, $field );
}

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

	return apply_filters( 'job_manager_prepare_uploaded_files', $files_to_upload );
}


add_action('wp_ajax_eaccounting_file_upload', function(){
	$data = [
		'files' => [],
	];

	if ( ! empty( $_FILES ) ) {
		foreach ( $_FILES as $file_key => $file ) {
			$files_to_upload = eaccounting_prepare_uploaded_files( $file );
			foreach ( $files_to_upload as $file_to_upload ) {
				$uploaded_file = eaccounting_upload_file(
					$file_to_upload,
					[
						'file_key' => $file_key,
					]
				);

				if ( is_wp_error( $uploaded_file ) ) {
					$data['files'][] = [
						'error' => $uploaded_file->get_error_message(),
					];
				} else {
					$data['files'][] = $uploaded_file;
				}
			}
		}
	}
	wp_send_json_success($data);
});
