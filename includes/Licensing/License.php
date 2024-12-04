<?php

namespace EverAccounting\Licensing;

use EverAccounting\Addons\Addon;

defined( 'ABSPATH' ) || exit;

/**
 * Class License
 *
 * @package EverAccounting\Licensing
 */
class License {
	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var Addon
	 */
	protected $addon;

	/**
	 * Addon shortname.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $shortname;

	/**
	 * License option.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $option_name;

	/**
	 * License data.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'license' => '',
		'status'  => '',
		'url'     => '',
		'expires' => '',
	);

	/**
	 * Client instance.
	 *
	 * @since 1.0.0
	 * @var Client
	 */
	protected $client;

	/**
	 * License constructor.
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

		$this->addon       = $addon;
		$this->client      = new Client();
		$this->shortname   = preg_replace( '/[^a-zA-Z0-9]/', '_', strtolower( $this->addon->get_slug() ) );
		$this->option_name = $this->shortname . '_license';
		$this->data        = wp_parse_args( (array) get_option( $this->option_name, array() ), $this->data );

		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	protected function register_hooks() {
		add_action( 'admin_notices', array( $this, 'license_notices' ) );
		add_action( 'plugin_action_links_' . $this->addon->get_basename(), array( $this, 'plugin_action_links' ) );
		add_action( 'after_plugin_row_' . $this->addon->get_basename(), array( $this, 'add_license_row' ), 10, 3 );
		add_action( 'wp_ajax_' . $this->addon->get_basename() . '_license_action', array( $this, 'handle_license_action' ) );
	}

	/**
	 * Display license notices.
	 *
	 * @since 1.0.0
	 */
	public function license_notices() {
		// determine if the current page is plugins.php.
		$screens = get_current_screen();
		if ( ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] )
			|| ( $screens && 'plugins' !== $screens->id )
			|| $this->is_valid()
			|| ! current_user_can( 'manage_options' )
			|| apply_filters( 'eac_hide_license_notices', false, $this->addon ) ) {
			return;
		}

		if ( empty( $this->get_key() ) ) {
			$message = sprintf(
			// translators: the extension name.
				__( 'Your license key for %1$s is missing. Please %2$s enter your license key %3$s to continue receiving updates and support.', 'wp-ever-accounting' ),
				'<strong>' . esc_html( $this->addon->get_name() ) . '</strong>',
				'<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">',
				'</a>'
			);

			printf( '<div class="notice notice-warning is-dismissible">%s</div>', wp_kses_post( wpautop( wptexturize( $message ) ) ) );
		} elseif ( $this->is_expired() ) {
			$message = sprintf(
			// translators: the extension name.
				__( 'Your license key for %1$s has expired. Please %2$s renew your license %3$s to continue receiving updates and support.', 'wp-ever-accounting' ),
				'<strong>' . esc_html( $this->addon->get_name() ) . '</strong>',
				'<a href="' . esc_url( $this->addon->get_plugin_uri() ) . '">',
				'</a>'
			);

			printf( '<div class="notice notice-warning is-dismissible">%s</div>', wp_kses_post( wpautop( wptexturize( $message ) ) ) );
		} elseif ( $this->is_disabled() ) {
			$message = sprintf(
			// translators: the extension name.
				__( 'You no longer have a valid license for %1$s. Please %2$s renew your license %3$s to continue receiving updates and support.', 'wp-ever-accounting' ),
				'<strong>' . esc_html( $this->addon->get_name() ) . '</strong>',
				'<a href="' . esc_url( $this->addon->get_plugin_uri() ) . '">',
				'</a>'
			);

			printf( '<div class="notice notice-warning is-dismissible">%s</div>', wp_kses_post( wpautop( wptexturize( $message ) ) ) );
		} elseif ( $this->is_moved() ) {
			$message = sprintf(
			// translators: the extension name.
				__( '%1$s - Your license key is not valid for this site. Please %2$s deactivate the license %3$s and %4$s activate it again %5$s.', 'wp-ever-accounting' ),
				'<strong>' . esc_html( $this->addon->get_name() ) . '</strong>'
			);

			printf( '<div class="notice notice-warning is-dismissible">%s</div>', wp_kses_post( wpautop( wptexturize( $message ) ) ) );
		}
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
		if ( ! current_user_can( 'manage_options' ) || ! $this->is_valid() ) {
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
		$visible  = $this->is_valid() ? 'hidden' : 'visible';
		$action   = $basename . '_license_action';
		$nonce    = wp_create_nonce( $basename . '_license_action' );
		?>
		<tr class="license-row notice-warning notice-alt plugin-update-tr <?php echo esc_attr( $visible ); ?>" data-plugin="<?php echo esc_attr( $this->addon->get_basename() ); ?>">
			<td colspan="<?php echo esc_attr( $colspan ); ?>" class="plugin-update colspanchange">
				<div class="update-message" style="margin-top: 15px;display: flex;flex-direction: row;align-items: center;flex-wrap: wrap;gap: 10px;">
					<?php if ( $this->is_valid() ) : ?>
						<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
						<span><?php esc_html_e( 'License is valid.', 'wp-ever-accounting' ); ?></span>
					<?php else : ?>
						<span class="dashicons dashicons-warning" style="color: #dc3232;"></span>
						<span><?php echo wp_kses_post( $this->get_message() ); ?></span>
					<?php endif; ?>
					<input
						class="regular-text license-key"
						type="text"
						placeholder="<?php esc_attr_e( 'Enter your license key', 'wp-ever-accounting' ); ?>"
						value="<?php echo esc_attr( $this->get_key() ); ?>"
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
					<?php if ( $this->is_valid() ) : ?>
						<button
							class="button license-button"
							data-action="<?php echo esc_attr( $action ); ?>"
							data-operation="deactivate"
							data-nonce="<?php echo esc_attr( $nonce ); ?>"
							style="line-height: 20px;">
							<span class="dashicons dashicons-no-alt"></span>
							<?php esc_html_e( 'Deactivate', 'wp-ever-accounting' ); ?>
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
		$operation = isset( $_POST['operation'] ) ? sanitize_text_field( wp_unslash( $_POST['operation'] ) ) : '';
		$license   = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';

		if ( empty( $license ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a valid license key and try again.', 'wp-ever-accounting' ) ) );
		}

		switch ( $operation ) {
			case 'activate':
				$data            = array();
				$host            = wp_parse_url( site_url(), PHP_URL_HOST );
				$response        = $this->client->activate( $license, $this->addon->get( 'item_id' ), $host );
				$is_valid        = $response->success && $response->license && 'valid' === $response->license;
				$data['url']     = $host;
				$data['status']  = ! $response->success && ! empty( $response->error ) ? sanitize_key( $response->error ) : sanitize_key( $response->license );
				$data['expires'] = ! empty( $response->expires ) ? $response->expires : '';
				$data['license'] = $is_valid ? $license : '';

				$this->update_data( $data );
				set_site_transient( 'update_plugins', null );

				wp_send_json_success(
					array(
						'message' => esc_html__( 'License key activated successfully.', 'wp-ever-accounting' ),
						'reload'  => true,
					)
				);

				break;
			case 'deactivate':
				if ( empty( $this->get_key() ) ) {
					wp_send_json_error( array( 'message' => esc_html__( 'Your license key is missing.', 'wp-ever-accounting' ) ) );
				}
				$response = $this->client->deactivate( $license, $this->addon->get( 'item_id' ), home_url() );
				set_site_transient( 'update_plugins', null );
				if ( $response->success ) {
					$this->update_data(
						array(
							'status'  => 'inactive',
							'expires' => ! empty( $response->expires ) ? $response->expires : '',
						)
					);

					wp_send_json_success(
						array(
							'message' => esc_html__( 'License key deactivated successfully.', 'wp-ever-accounting' ),
							'reload'  => true,
						)
					);
				}

				wp_send_json_error( array( 'message' => esc_html__( 'Failed to deactivate the license key.', 'wp-ever-accounting' ) ) );

				break;
		}

		wp_send_json_error( array( 'message' => esc_html__( 'Invalid operation.', 'wp-ever-accounting' ) ) );
		wp_die();
	}

	/**
	 * Check if the license exists or not.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function exists() {
		return empty( $this->data['license'] ) ? false : true;
	}

	/**
	 * Determine if the license is valid.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_valid() {
		return 'valid' === $this->data['status'];
	}

	/**
	 * Determine if the license is expired.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_expired() {
		return 'expired' === $this->get_status();
	}

	/**
	 * Determine if the site is moved.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_moved() {
		$active_url = isset( $this->data['url'] ) ? $this->data['url'] : '';
		if ( empty( $active_url ) ) {
			return false;
		}

		$has_moved = wp_parse_url( site_url(), PHP_URL_HOST ) !== $active_url;
		if ( $has_moved && $this->is_valid() ) {
			$this->update_data(
				array(
					'status' => 'status',
					'error'  => 'site_inactive',
				)
			);
		}

		return $has_moved;
	}

	/**
	 * Is the license disabled?
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_disabled() {
		return 'disabled' === $this->get_status();
	}

	/**
	 * Get license data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Get the license key.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_key() {
		return array_key_exists( 'license', $this->data ) ? $this->data['license'] : '';
	}

	/**
	 * Get the license status.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_status() {
		return array_key_exists( 'status', $this->data ) ? $this->data['status'] : '';
	}

	/**
	 * Get error message.
	 *
	 * @param string $status The status.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_message( $status = '' ) {
		if ( empty( $status ) ) {
			$status = $this->get_status();
		}

		switch ( $status ) {
			case 'missing':
				$message = esc_html__( 'Your license key is missing.', 'wp-ever-accounting' );
				break;
			case 'valid':
				$message = esc_html__( 'Your license key is valid.', 'wp-ever-accounting' );
				break;
			case 'expired':
				$expires = ! empty( $this->license_data['expires'] && 'lifetime' !== $this->license_data['expires'] ) ? strtotime( $this->data['expires'] ) : 0;
				$expired = $expires > 0 && $expires > wp_date( 'U' ) && ( $expires - wp_date( 'U' ) < DAY_IN_SECONDS );
				if ( $expired ) {
					$message = sprintf(
					// translators: %s: license expiration date.
						__( 'Your license key has expired on %s.', 'wp-ever-accounting' ),
						esc_html( date_i18n( get_option( 'date_format' ), $expires ) )
					);
				} else {
					$message = __( 'Your license key has expired.', 'wp-ever-accounting' );
				}
				break;
			case 'revoked':
			case 'disabled':
				$message = esc_html__( 'Your license key has been disabled.', 'wp-ever-accounting' );
				break;
			case 'site_inactive':
				$message = esc_html__( 'Your license key is not valid for this site.', 'wp-ever-accounting' );
				break;
			case 'invalid':
			case 'invalid_item_id':
			case 'item_name_mismatch':
			case 'key_mismatch':
				$message = esc_html__( 'This appears to be an invalid license key.', 'wp-ever-accounting' );
				break;
			case 'no_activations_left':
				$message = esc_html__( 'Your license key has reached its activation limit.', 'wp-ever-accounting' );
				break;
			case 'license_not_activable':
				$message = esc_html__( 'The key you entered belongs to a bundle, please use the product specific license key.', 'wp-ever-accounting' );
				break;
			default:
				$message = esc_html__( 'Your license key is invalid.', 'wp-ever-accounting' );
				break;
		}

		return $message;
	}

	/**
	 * Update the license data.
	 *
	 * @param array $data The license data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function update_data( $data ) {
		if ( is_array( $data ) && ! empty( $data ) ) {
			$this->data = wp_parse_args( $data, $this->data );
			update_option( $this->option_name, $this->data );
		}
	}
}
