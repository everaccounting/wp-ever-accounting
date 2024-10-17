<?php
/**
 * Admin Attachment View.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var $attachment \EverAccounting\Models\Attachment Attachment object.
 */

defined( 'ABSPATH' ) || exit;
if ( empty( $attachment ) ) {
	echo '<p>' . esc_html__( 'Attachment not found.', 'wp-ever-accounting' ) . '</p>';

	return;
}

?>
<div class="eac-file-upload has--file">
	<div class="eac-file-upload__preview">
		<div class="eac-file-upload__icon">
			<?php echo wp_get_attachment_image( $attachment->id, 'thumbnail' ); ?>
		</div>
		<div class="eac-file-upload__info">
			<div class="eac-file-upload__name">
				<?php echo wp_get_attachment_link( $attachment->id, 'full', false, true, $attachment->title, array( 'target' => '_blank' ) ); ?>
			</div>
			<div class="eac-file-upload__size"><?php echo esc_html( size_format( $attachment->filesize, 2 ) ); ?></div>
		</div>
	</div>
</div>
