<?php
/**
 * Admin View: Note item
 *
 * @since 2.0.0
 * @package EverAccounting
 * @subpackage Admin/Views
 *
 * @var Note $note Notes.
 */

use EverAccounting\Models\Note;

defined( 'ABSPATH' ) || exit();

$author = esc_html__( 'System', 'wp-ever-accounting' );
if ( $note->creator_id ) {
	$user_object = get_userdata( $note->creator_id );
	if ( $user_object ) {
		$author = ! empty( $user_object->display_name ) ? $user_object->display_name : $user_object->user_login;
	}
}

?>
<li class="note" id="note-<?php echo esc_attr( $note->id ); ?>">
	<div class="note__content">
		<?php echo wp_kses_post( wpautop( wptexturize( make_clickable( $note->content ) ) ) ); ?>
	</div>
	<div class="note__meta">
		<abbr class="exact-date" title="<?php echo esc_attr( $note->created_at ); ?>">
			<?php echo esc_html( date_i18n( eac_date_time_format(), strtotime( $note->created_at ) ) ); ?>
			<?php // translators: %s: note author. ?>
			<?php echo esc_html( sprintf( ' ' . __( 'by %s', 'wp-ever-accounting' ), $author ) ); ?>
		</abbr>
		<a href="#" class="note__delete" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eac_delete_note' ) ); ?>" data-note_id="<?php echo esc_attr( $note->id ); ?>">
			<?php echo esc_html_x( 'Delete', 'Delete', 'wp-ever-accounting' ); ?>
		</a>
	</div>
</li>