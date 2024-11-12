<?php

namespace EverAccounting\Addons;

defined( 'ABSPATH' ) || exit;

/**
 * Activator class.
 *
 * @since 1.0.0
 * @package EverAccounting
 */
class Activator {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var Addon
	 */
	protected $addon;

	/**
	 * Activator constructor.
	 *
	 * @param Addon $addon The addon.
	 *
	 * @throws \InvalidArgumentException If the addon is not an instance of Addon.
	 * @since 1.0.0
	 */
	public function __construct( $addon ) {
		// if the addon is not an extended class of Addon, throw an exception.
		if ( ! is_subclass_of( $addon, Addon::class ) ) {
			throw new \InvalidArgumentException( 'The addon must be an instance of Addon.' );
		}

		$this->addon = $addon;
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function register_hooks() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'plugin_action_links_' . $this->addon->get_basename(), array( $this, 'plugin_action_links' ) );
		add_action( 'after_plugin_row_' . $this->addon->get_basename(), array( $this, 'add_license_row' ), 10, 3 );
		add_action( 'wp_ajax_' . $this->addon->get_basename() . '_license_action', array( $this, 'handle_license_action' ) );
	}

	/**
	 * Display admin notices.
	 *
	 * @since 1.0.0
	 */
	public function admin_notices() {
		if ( ! current_user_can( 'manage_options' ) || $this->addon->license->is_valid() ) {
			return;
		}
		$notice = sprintf(
		// translators: %1$s: <a> tag start, %2$s: <a> tag end, %3$s: plugin name.
			__( '%1$s is not active. Please %2$sactivate%3$s to unlock access to updates, security enhancements, support, and more.', 'wp-ever-accounting' ),
			'<strong>' . esc_html( $this->addon->name ) . '</strong>',
			'<a href="' . admin_url( 'plugins.php' ) . '">',
			'</a>'
		);
		echo '<div class="notice notice-error is-dismissible"><p>' . wp_kses_post( $notice ) . '</p></div>';
	}

	/**
	 * Add plugin action links.
	 *
	 * @param array $links The plugin action links.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		if ( ! current_user_can( 'manage_options' ) || ! $this->addon->license->is_valid() ) {
			return $links;
		}
		$links['license'] = sprintf(
			'<a href="javascript:void(0);" class="license-manage-link" aria-label="%1$s">%1$s</a>',
			__( 'License', 'wp-ever-accounting' )
		);

		return $links;
	}

	/**
	 * Display plugin row.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_license_row() {
		$screen   = get_current_screen();
		$columns  = get_column_headers( $screen );
		$colspan  = ! is_countable( $columns ) ? 3 : count( $columns );
		$basename = $this->addon->get_basename();
		$visible  = $this->addon->license->is_valid() ? 'hidden' : 'visible';
		$action   = $basename . '_license_action';
		$nonce    = wp_create_nonce( $basename . '_license_action' );
		?>
		<tr class="license-row notice-warning notice-alt plugin-update-tr <?php echo esc_attr( $visible ); ?>" data-plugin="<?php echo esc_attr( $this->addon->get_basename() ); ?>">
			<td colspan="<?php echo esc_attr( $colspan ); ?>" class="plugin-update colspanchange">
				<div class="update-message" style="margin-top: 15px;display: flex;flex-direction: row;align-items: center;flex-wrap: wrap;gap: 10px;">
					<?php if ( $this->addon->license->is_valid() ) : ?>
						<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
						<span><?php esc_html_e( 'License is valid.', 'wp-ever-accounting' ); ?></span>
					<?php else : ?>
						<span class="dashicons dashicons-warning" style="color: #dc3232;"></span>
						<span><?php echo wp_kses_post( $this->addon->license->get_message() ); ?></span>
					<?php endif; ?>
					<input
						class="regular-text license-key"
						type="text"
						placeholder="<?php esc_attr_e( 'Enter your license key', 'wp-ever-accounting' ); ?>"
						value="<?php echo esc_attr( $this->addon->license->get_key() ); ?>"
						style="width: 18em;margin-right:-10px; border-top-right-radius:0; border-bottom-right-radius:0; border-right:0;"
					>
					<button
						class="button license-button"
						data-action="<?php echo esc_attr( $action ); ?>"
						data-operation="activate"
						data-nonce="<?php echo esc_attr( $nonce ); ?>"
						style="line-height: 20px;border-top-left-radius:0; border-bottom-left-radius:0;">
						<span class="dashicons dashicons-admin-network"></span>
						<?php esc_html_e( 'Activate', 'wp-ever-accounting' ); ?>
					</button>
					<?php if ( $this->addon->license->is_valid() ) : ?>
						<button
							class="button license-button"
							data-action="<?php echo esc_attr( $action ); ?>"
							data-operation="deactivate"
							data-nonce="<?php echo esc_attr( $nonce ); ?>"
							style="line-height: 20px;">
							<span class="dashicons dashicons-no-alt"></span>
							<?php esc_html_e( 'Deactivate', 'wp-ever-accounting' ); ?>
						</button>
						<button
							class="button license-button"
							data-action="<?php echo esc_attr( $action ); ?>"
							data-operation="check"
							data-nonce="<?php echo esc_attr( $nonce ); ?>"
							style="line-height: 20px;">
							<span class="dashicons dashicons-update"></span>
							<?php esc_html_e( 'Check', 'wp-ever-accounting' ); ?>
						</button>
					<?php endif; ?>
					<span class="spinner"></span>
					<script type="application/javascript">
						addEventListener('DOMContentLoaded', () => {
							// check if Jquery is loaded. If not load return.
							if (typeof jQuery !== 'undefined') {
								jQuery(function ($) {
									$(document.body)
										.on('click', '[data-plugin="<?php echo esc_attr( $basename ); ?>"] .license-manage-link', function (e) {
											e.preventDefault();
											const plugin = $(this).closest('tr').data('plugin');
											$(this).closest('tr').siblings('.license-row[data-plugin="' + plugin + '"]').toggle();
										})
										.on('click', '[data-plugin="<?php echo esc_attr( $basename ); ?>"] .license-button', function (e) {
											e.preventDefault();
											var $this = $(this);
											$this.closest('tr').find('.spinner').addClass('is-active');
											$this.closest('tr').find('.license-button').prop('disabled', true);
											wp.ajax.post({
												action: $this.data('action'),
												operation: $this.data('operation'),
												nonce: $this.data('nonce'),
												license_key: $this.closest('tr').find('.license-key').val(),
											}).always(function (response) {
												$this.closest('tr').find('.spinner').removeClass('is-active');
												$this.closest('tr').find('.license-button').prop('disabled', false);
												if (response && response.message) {
													alert(response.message);
												}
												if (response.reload) {
													location.reload();
												}
											})
										});
								});
							}
						});
					</script>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * License AJAX handler.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function handle_license_action() {
		check_ajax_referer( $this->addon->get_basename() . '_license_action', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to perform this action.', 'wp-ever-accounting' ) ) );
		}
		$operation   = isset( $_POST['operation'] ) ? sanitize_text_field( wp_unslash( $_POST['operation'] ) ) : '';
		$license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';

		if ( empty( $license_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a valid license key and try again.', 'wp-ever-accounting' ) ) );
		}

		switch ( $operation ) {
			case 'activate':
				$result = $this->addon->license->activate( $license_key );
				break;
			case 'deactivate':
				$result = $this->addon->license->deactivate();
				break;
			case 'check':
				$result = $this->addon->license->check();
				break;
			default:
				$result = false;
				break;
		}
	}
}
