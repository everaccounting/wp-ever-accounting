<?php
/**
 * Shows notes
 *
 * @package EverAccounting\Admin
 * @var \EverAccounting\Models\Note[]  $notes The item being displayed
 */

?>
<?php
if ( empty( $notes ) ) {
	echo sprintf( '<p>%s</p>', __( 'There are no notes yet.', 'wp-ever-accounting' ) );
} else {
	?>
	<ul class="ea-invoice-notes">
		<?php foreach ( $notes as $note ) : ?>
			<li class="ea-invoice-notes__item" data-noteid="<?php esc_attr( $note->get_id() ); ?>">
				<div class="ea-invoice-notes__item-content">
					<?php echo wp_kses_post( $note->get_note() ); ?>
				</div>
				<div class="ea-invoice-notes__item-meta">
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
					<?php /* translators: %s note creator user */ ?>
					<?php echo sprintf( esc_html__( 'By %s', 'wp-ever-accounting' ), $note->get_author() ); ?>
					<a href="#" class="delete_note" role="button"><?php esc_html_e( 'Delete note', 'wp-ever-accounting' ); ?></a>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php
}
