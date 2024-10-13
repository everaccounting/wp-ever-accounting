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

$author = esc_html__( 'Unknown', 'wp-ever-accounting' );
if ( $note->creator_id ) {
	$user_object = get_userdata( $note->creator_id );
	if ( $user_object ) {
		$author = ! empty( $user_object->display_name ) ? $user_object->display_name : $user_object->user_login;
	}
}

?>
<li class="note" id="note-<?php echo esc_attr( $note->id ); ?>">
	<div class="note__header">
		<strong><?php echo esc_html( $author ); ?></strong>
		<time datetime="<?php echo esc_attr( $note->created_at ); ?>"> <?php echo esc_html( $note->created_at ); ?></time>

		<a href="#" class="note__delete" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eac_delete_note' ) ); ?>" data-note_id="<?php echo esc_attr( $note->id ); ?>">
			<?php echo esc_html_x( '&times;', 'Delete note', 'wp-ever-accounting' ); ?>
		</a>

	</div>
	<?php echo wp_kses_post( wpautop( wptexturize( make_clickable( $note->content ) ) ) ); ?>
</li>
