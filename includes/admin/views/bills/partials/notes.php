<?php
/**
 * Shows notes
 * Used in view bill page.
 *
 * @package EverAccounting\Admin
 * @var Bill $bill The item being used
 */

use EverAccounting\Models\Bill;

$notes = eaccounting_get_notes(
	array(
		'number'    => - 1,
		'parent_id' => $bill->get_id(),
		'type'      => 'bill',
		'orderby'   => 'date_created',
		'order'     => 'DESC',
	)
);
?>
<div class="ea-card__body" id="ea-bill-notes">
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

