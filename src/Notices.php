<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;


/**
 * Class Notices.
 *
 * Notices are used to display messages to the admin user. Whereas messages are used to display messages to the user on the frontend.
 * notice types: error, warning, success, info. message types: error, warning, success, info.
 * notice could be dismissed. message could not be dismissed.
 *
 * This class is used to display notices in the admin area and on the frontend.
 *
 * @since 1.1.6
 * @package EverAccounting
 */
class Notices extends Singleton {

	/**
	 * Notices.
	 *
	 * @since 1.1.6
	 * @var array
	 */
	private $notices = array();
	/**
	 * Messages.
	 *
	 * @since 1.1.6
	 * @var array
	 */
	private $messages = array();

	/**
	 * Notices constructor.
	 *
	 * @since 1.1.6
	 */
	protected function __construct() {
		add_action( 'init', array( $this, 'load_notices' ), 10 );
		add_filter( 'wp_redirect', array( $this, 'redirect' ), 1 );
		// add_filter( 'wp_shutdown', array( $this, 'save_notices' ), PHP_INT_MAX );
		add_action( 'wp_ajax_eac_dismiss_notice', array( $this, 'dismiss_notice' ) );
		add_action( 'admin_notices', array( $this, 'display_notices' ) );
		add_action( 'admin_footer', array( $this, 'display_notices' ) );
		add_action( 'ever_accounting_messages', array( $this, 'display_messages' ) );
		add_action( 'admin_footer', array( $this, 'dismiss_notice_script' ) );
	}

	/**
	 * Load notices.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function load_notices() {
		// when a user is logged in the notices are stored in the user meta.
		// when a user is not logged in the notices are stored in the transient based on the session id.
		foreach ( array( 'notices', 'messages' ) as $type ) {
			// if the user is logged in.
			if ( is_user_logged_in() ) {
				$notices = get_user_meta( get_current_user_id(), 'eac_' . $type, true );
				// if the notices are not empty.
			} else {
				// Take the first 8 characters of the session token.
				$token   = substr( wp_get_session_token(), 0, 8 );
				$notices = get_transient( 'eac_' . $type . '_' . $token );
				// if the notices are not empty.
			}
			if ( ! empty( $notices ) && is_array( $notices ) ) {
				$this->{$type} = array_merge( $this->{$type}, $notices );
			}
		}
	}

	/**
	 * Redirect.
	 *
	 * @since 1.1.6
	 * @param string $location Location.
	 *
	 * @return string
	 */
	public function redirect( $location ) {
		$this->save_notices();
		return $location;
	}

	/**
	 * Save notices.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function save_notices() {
		// when a user is logged in the notices are stored in the user meta.
		// when a user is not logged in the notices are stored in the transient based on the session id.
		foreach ( array( 'notices', 'messages' ) as $type ) {
			// if the notices are not empty.
			if ( ! empty( $this->{$type} ) ) {
				// if the user is logged in.
				if ( is_user_logged_in() ) {
					update_user_meta( get_current_user_id(), 'eac_' . $type, $this->{$type} );
				} else {
					// Take the first 8 characters of the session token.
					$token = substr( wp_get_session_token(), 0, 8 );
					set_transient( 'eac_' . $type . '_' . $token, $this->{$type}, 60 * 60 * 24 );
				}
			}
		}
	}

	/**
	 * Dismiss notice.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function dismiss_notice() {
		check_admin_referer( 'eac_dismiss_notice', 'security' );
		$notice_id = isset( $_POST['notice_id'] ) ? sanitize_text_field( wp_unslash( $_POST['notice_id'] ) ) : '';
		if ( ! empty( $notice_id ) ) {
			$dismissed = get_user_meta( get_current_user_id(), 'eac_dismissed_notices', true );
			if ( empty( $dismissed ) ) {
				$dismissed = array();
			}
			$dismissed[] = $notice_id;
			update_user_meta( get_current_user_id(), 'eac_dismissed_notices', $dismissed );
		}
	}

	/**
	 * Display notices.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function display_notices() {
		if ( empty( $this->notices ) ) {
			return;
		}
		foreach ( $this->notices as $notice ) {
			$message     = isset( $notice['message'] ) ? $notice['message'] : '';
			$id          = isset( $notice['id'] ) ? $notice['id'] : substr( md5( $message ), 0, 8 );
			$type        = isset( $notice['type'] ) ? $notice['type'] : '';
			$args        = isset( $notice['args'] ) && is_array( $notice['args'] ) ? $notice['args'] : array();
			$dismissible = isset( $args['dismissible'] ) ? $args['dismissible'] : false;

			// If dismissible, and already dismissed or no message, don't display.
			if ( ( $dismissible && self::is_dismissed( $id ) ) || empty( $message ) ) {
				continue;
			}
			$classes = array( 'eac-notice notice notice-' . $type );
			if ( $dismissible ) {
				$classes[] = 'is-dismissible';
			}
			if ( ! empty( $args['class'] ) ) {
				$classes[] = $args['class'];
			}
			echo sprintf(
				'<div id="%s" class="%s" data-notice-id="%s" data-security="%s">%s</div>',
				esc_attr( $id ),
				esc_attr( implode( ' ', $classes ) ),
				esc_attr( $id ),
				esc_attr( wp_create_nonce( 'eac_dismiss_notice' ) ),
				wp_kses_post( wpautop( wptexturize( $message ) ) )
			);

			// If not dismissible, remove it from the array.
			if ( ! $dismissible ) {
				$notice_ids = wp_list_pluck( $this->notices, 'id' );
				$key        = array_search( $id, $notice_ids, true );
				if ( false !== $key ) {
					unset( $this->notices[ $key ] );
				}
			}

			update_user_meta( get_current_user_id(), 'eac_notices', $this->notices );
		}
	}

	/**
	 * Display messages.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function display_messages() {
		if ( empty( $this->messages ) ) {
			return;
		}
		foreach ( $this->messages as $message ) {
			$message = isset( $message['message'] ) ? $message['message'] : '';
			$type    = isset( $message['type'] ) ? $message['type'] : '';
			$args    = isset( $message['args'] ) && is_array( $message['args'] ) ? $message['args'] : array();
			$classes = array( 'eac-message notice notice-' . $type );
			if ( ! empty( $args['class'] ) ) {
				$classes[] = $args['class'];
			}
			echo sprintf(
				'<div class="%s">%s</div>',
				esc_attr( implode( ' ', $classes ) ),
				wp_kses_post( wpautop( wptexturize( $message ) ) )
			);
		}
	}

	/**
	 * Dismiss notice script.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function dismiss_notice_script() {
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				$( document ).on( 'click', '.eac-notice .notice-dismiss', function() {
					var $notice = $( this ).closest( '.eac-notice' );
					var notice_id = $notice.data( 'notice-id' );
					var security = $notice.data( 'security' );
					$.ajax( {
						url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
						type: 'POST',
						data: {
							action: 'eac_dismiss_notice',
							notice_id: notice_id,
							security: security
						},
						success: function( response ) {
							$notice.fadeOut( 'fast' );
						}
					} );
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Is notice dismissed.
	 *
	 * @param string $id Notice id.
	 *
	 * @since 1.1.6
	 * @return bool
	 */
	public static function is_dismissed( $id ) {
		$dismissed = get_user_meta( get_current_user_id(), 'eac_dismissed_notices', true );
		if ( ! empty( $dismissed ) && in_array( $id, $dismissed, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add notices.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function add_core_notices() {}

	/**
	 * Add admin notice.
	 *
	 * @param string $message Message to display.
	 * @param string $type Type of notice. Default is 'success'.
	 * @param array  $args Additional arguments.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function add_notice( $message, $type = 'success', $args = array() ) {
		$defaults = array(
			'id'          => substr( md5( $message ), 0, 8 ),
			'dismissible' => false,
		);
		$args     = wp_parse_args( $args, $defaults );

		// If empty message.
		if ( empty( $message ) ) {
			return;
		}
		var_dump( $message );
		// if the notice already dismissed then return.
		if ( true === self::is_dismissed( $args['id'] ) && self::is_dismissed( $args['id'] ) ) {
			return;
		}

		// Check if the notice is already added.
		$notice_ids = wp_list_pluck( self::get_instance()->notices, 'id' );
		$notice     = array(
			'id'      => $args['id'],
			'message' => $message,
			'type'    => $type,
			'args'    => $args,
		);
		// if the notice is not already added.
		if ( ! in_array( $args['id'], $notice_ids, true ) ) {
			self::get_instance()->notices[] = $notice;
		}
	}

	/**
	 * Add message.
	 *
	 * @param string $message Message to display.
	 * @param string $type Type of message. Default is 'success'.
	 * @param array  $args Additional arguments.
	 */
	public static function add_message( $message, $type = 'success', $args = array() ) {
		if ( empty( $args['id'] ) ) {
			$args['id'] = substr( md5( $message ), 0, 8 );
		}
		if ( empty( $message ) ) {
			return;
		}

		$message_ids = wp_list_pluck( self::get_instance()->messages, 'id' );
		$message     = array(
			'id'      => $args['id'],
			'message' => $message,
			'type'    => $type,
			'args'    => $args,
		);
		if ( ! in_array( $args['id'], $message_ids, true ) ) {
			self::get_instance()->messages[] = $message;
		}
	}
}
