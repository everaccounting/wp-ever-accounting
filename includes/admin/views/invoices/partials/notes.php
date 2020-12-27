<?php
/**
 * Shows notes
 * Used in view invoice page.
 *
 * @package EverAccounting\Admin
 * @var \EverAccounting\Models\Invoice $invoice The item being used
 */

$notes = eaccounting_get_notes(
	array(
		'number'    => - 1,
		'parent_id' => $invoice->get_id(),
		'type'      => 'invoice',
	)
);
?>

<div class="ea-card" id="ea-invoice-notes">
	<div class="ea-card__header">
		<h3 class="ea-card__title"><?php _e( 'Invoice Notes', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="ea-card__body">
		<?php if ( empty( $notes ) ) : ?>
			<p class="ea-card__inside"><?php esc_html_e( 'There are no notes yet.', 'wp-ever-accounting' ); ?></p>

		<?php else : ?>
			<ul class="ea-document-notes">
				<?php foreach ( $notes as $note ) : ?>
					<li class="ea-document-notes__item" data-noteid="<?php esc_attr( $note->get_id() ); ?>">
						<div class="ea-document-notes__item-content">
							<?php echo wp_kses_post( $note->get_note() ); ?>
						</div>
						<div class="ea-document-notes__item-meta">
							<abbr class="exact-date" title="<?php echo esc_attr( $note->get_date_created() ); ?>">
								<?php
								echo sprintf(
								/* translators: %s note creator user */
									esc_html__( 'added on %1$s at %2$s', 'wp-ever-accounting' ),
									eaccounting_format_datetime( $note->get_date_created(), 'F m, Y' ),
									eaccounting_format_datetime( $note->get_date_created(), 'H:i a' )
								);
								?>
							</abbr>
							<a href="#" class="delete_note" role="button"><?php esc_html_e( 'Delete note', 'wp-ever-accounting' ); ?></a>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>

		<?php endif; ?>

	</div>
	<div class="ea-card__body">
		<form id="invoice-note-insert" method="post" class="ea-document-notes__add">
			<p>
				<label for="invoice_note"><?php _e( 'Add note', 'wp-ever-accounting' ); ?></label>
				<textarea type="text" name="document_note" id="document_note" class="input-text" cols="20" rows="5" autocomplete="off" spellcheck="false"></textarea>
			</p>

			<p>
				<label for="document_note_type" class="screen-reader-text"><?php _e( 'Note type', 'wp-ever-accounting' ); ?></label>
				<select name="document_note_type" id="document_note_type">
					<option value=""><?php _e( 'Private note', 'wp-ever-accounting' ); ?></option>
					<option value="customer"><?php _e( 'Note to customer', 'wp-ever-accounting' ); ?></option>
				</select>
				<button type="button" class="add_document_note button"><?php _e( 'Add', 'wp-ever-accounting' ); ?></button>
			</p>
			<input type="hidden" name="action" value="eaccounting_add_invoice_note">
			<input type="hidden" name="invoice_id" value="<?php esc_attr( $invoice->get_id() ); ?>">
			<?php wp_nonce_field( 'ea_add_invoice_note', 'nonce' ); ?>
		</form>
	</div>
</div>
