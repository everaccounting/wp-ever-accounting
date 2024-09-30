<?php
/**
 * Admin View: Customer details.
 *
 * @since 1.0.0
 *
 * @subpackage EverAccounting/Admin/Views
 * @package EverAccounting
 * @var $customer \EverAccounting\Models\Customer Customer object.
 */

defined( 'ABSPATH' ) || exit;
?>

<h1 class="wp-heading-inline">
	<?php printf( esc_html__( 'Customer: #%d', 'wp-ever-accounting' ), esc_html( $customer->id ) ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<div class="eac-card">
	<div class="eac-card__faked"></div>
	<div class="eac-profile-header">
		<div class="eac-profile-header__avatar">
			<?php echo get_avatar( $customer->email, 120 ); ?>
		</div>
		<div class="eac-profile-header__columns">
			<div class="eac-profile-header__column">
				<div class="eac-profile-header__title">
					Tremblay and Rath
				</div>
				<p>XYZ Company</p>
				<p>22, Ave Street, Newyork, USA</p>
			</div>
			<div class="eac-profile-header__column">

			</div>
		</div>
	</div>
	<ul class="eac-profile-nav" role="tablist">
		<li>
			<a href="#"  class="active" aria-selected="true" role="tab">
				<?php esc_html_e( 'Overview', 'wp-ever-accounting' ); ?>
			</a>
		</li>
		<li>
			<a href="#" aria-selected="false" role="tab">
				<?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?>
			</a>
		</li>
	</ul>
</div>

<div class="eac-poststuff is--alt">
	<div class="column-1">

		<div class="eac-card">
			<table class="eac-table">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Type', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Status', 'wp-ever-accounting' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>2021-09-01</td>
					<td>Invoice</td>
					<td>$100.00</td>
					<td>Paid</td>
				</tr>
				<tr>
					<td>2021-09-01</td>
					<td>Invoice</td>
					<td>$100.00</td>
					<td>Paid</td>
				</tr>
				<tr>
					<td>2021-09-01</td>
					<td>Invoice</td>
					<td>$100.00</td>
					<td>Paid</td>
				</tr>
				</tbody>
			</table>
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Notes', 'wc-serial-numbers' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<form action="">
					<div class="eac-form-field">
						<label for="note"><?php esc_html_e( 'Add Note', 'wc-serial-numbers' ); ?></label>
						<textarea name="note" id="note" cols="30" rows="2" required="required" placeholder="Enter Note"></textarea>
					</div>
					<input type="hidden" name="object_id" value="">
					<input type="hidden" name="object_type" value="payment">
					<?php wp_nonce_field( 'wcsn_add_note' ); ?>
					<button class="button"><?php esc_html_e( 'Add Note', 'wc-serial-numbers' ); ?></button>
				</form>
				<br>
				<ul class="eac-notes">
					<li class="note">
						<div class="note__header">
							<div class="note__author">
								<?php echo get_avatar( get_current_user_id(), 32 ); ?>
								<span
									class="note__author-name"><?php echo get_the_author_meta( 'display_name', get_current_user_id() ); ?></span>
							</div>
							<div class="note__date">
								<?php echo date_i18n( 'M d, Y', strtotime( current_time( 'mysql' ) ) ); ?>
							</div>
						</div>
						<div class="note__content">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, quibusdam.</p>
						</div>
						<div class="note__actions">
							<a href="#"
							   class="note__action"><?php esc_html_e( 'Delete', 'wc-serial-numbers' ); ?></a>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="column-2">
		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Customer Information', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-list has--split has--hover">
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo esc_html( $customer->name ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Email', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo esc_html( $customer->email ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Phone', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo esc_html( $customer->phone ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Website', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo esc_html( $customer->website ); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
