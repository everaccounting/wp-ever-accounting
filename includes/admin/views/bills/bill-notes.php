<?php
/**
 * Shows notes
 * Used in view bill page.
 *
 * @package EverAccounting\Admin
 * @var Bill $bill The item being used
 */

use EverAccounting\Models\Bill;
//
//$notes = eaccounting_get_notes(
//	array(
//		'number'    => - 1,
//		'parent_id' => $bill->get_id(),
//		'type'      => 'bill',
//		'orderby'   => 'date_created',
//		'order'     => 'DESC',
//	)
//);
?>
<<<<<<< HEAD
<div id="ea-bill_notes-body">
	<div class="ea-card__inside">
		<?php if ( empty( $notes ) ) : ?>
			<p class="ea-card__inside"><?php esc_html_e( 'There are no notes yet.', 'wp-ever-accounting' ); ?></p>

		<?php else : ?>
			<ul class="ea-document-notes">
				<?php foreach ( $notes as $note ) : ?>
					<li class="ea-document-notes__item" data-noteid="<?php echo esc_attr( $note->get_id() ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ea_delete_note' ) ); ?>">
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
	<div class="ea-card__footer">
		<form id="bill-note-form" method="post" class="ea-document-notes__form">
			<p class="form-field">
				<label for="bill_note"><?php _e( 'Add note', 'wp-ever-accounting' ); ?></label>
				<textarea type="text" name="note" class="input-text" cols="20" rows="5" autocomplete="off" spellcheck="false" required></textarea>
			</p>
=======
<!--<div class="ea-card__body" id="ea-bill-notes">-->
<!--	--><?php //if ( empty( $notes ) ) : ?>
<!--		<p class="ea-card__inside">--><?php //esc_html_e( 'There are no notes yet.', 'wp-ever-accounting' ); ?><!--</p>-->
<!---->
<!--	--><?php //else : ?>
<!--		<ul class="ea-document-notes">-->
<!--			--><?php //foreach ( $notes as $note ) : ?>
<!--				<li class="ea-document-notes__item" data-noteid="--><?php //echo esc_attr( $note->get_id() ); ?><!--" data-nonce="--><?php //echo esc_attr( wp_create_nonce( 'ea_delete_note' ) ); ?><!--">-->
<!--					<div class="ea-document-notes__item-content">-->
<!--						--><?php //echo wp_kses_post( $note->get_note() ); ?>
<!--					</div>-->
<!--					<div class="ea-document-notes__item-meta">-->
<!--						<abbr class="exact-date" title="--><?php //echo esc_attr( $note->get_date_created() ); ?><!--">-->
<!--							--><?php
//							echo sprintf(
//							/* translators: %s note creator user */
//								esc_html__( 'added on %1$s at %2$s', 'wp-ever-accounting' ),
//								eaccounting_format_datetime( $note->get_date_created(), 'F m, Y' ),
//								eaccounting_format_datetime( $note->get_date_created(), 'H:i a' )
//							);
//							?>
<!--						</abbr>-->
<!--						<a href="#" class="delete_note" role="button">--><?php //esc_html_e( 'Delete note', 'wp-ever-accounting' ); ?><!--</a>-->
<!--					</div>-->
<!--				</li>-->
<!--			--><?php //endforeach; ?>
<!--		</ul>-->
<!---->
<!--	--><?php //endif; ?>
<!---->
<!--</div>-->
<?php eaccounting_get_admin_template( 'bills/partials/notes', array( 'bill' => $bill ) ); ?>
<div class="ea-card__body">
	<form id="bill-note-insert" method="post" class="ea-document-notes__add">
		<p>
			<label for="bill_note"><?php _e( 'Add note', 'wp-ever-accounting' ); ?></label>
			<textarea type="text" name="note"  class="input-text" cols="20" rows="5" autocomplete="off" spellcheck="false" required></textarea>
		</p>
>>>>>>> 85484834cb2fabb0d47b021004587f3f1e779551

			<button type="submit" class="add_document_note button"><?php _e( 'Add', 'wp-ever-accounting' ); ?></button>
			<input type="hidden" name="action" value="eaccounting_add_bill_note">
			<input type="hidden" name="bill_id" value="<?php echo esc_attr( $bill->get_id() ); ?>">
			<?php wp_nonce_field( 'ea_add_bill_note', 'nonce' ); ?>
		</form>
	</div>
</div>
