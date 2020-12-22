<?php
/**
 * Attachment Trait
 */

namespace EverAccounting\Traits;

defined( 'ABSPATH' ) || exit;

trait AttachmentTrait {
	/**
	 * Get attachment.
	 *
	 * @since 1.1.0
	 * @return false|\stdClass
	 */
	public function get_attachment() {
		if ( is_callable( array( $this, 'get_attachment_id' ) ) ) {
			$attachment_id = $this->get_attachment_id();
		} elseif ( is_callable( array( $this, 'get_image_id' ) ) ) {
			$attachment_id = $this->get_image_id();
		} elseif ( is_callable( array( $this, 'get_avatar_id' ) ) ) {
			$attachment_id = $this->get_avatar_id();
		} else {
			$attachment_id = false;
		}

		if ( ! empty( $attachment_id ) && 'attachment' === get_post_type( $attachment_id ) ) {
			$attachment   = get_post( $attachment_id );
			$output       = new \stdClass();
			$output->id   = $attachment->ID;
			$output->name = $attachment->post_title;
			$output->src  = $attachment->guid;

			return $output;
		}

		return false;
	}
}
