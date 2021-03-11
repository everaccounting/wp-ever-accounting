<?php
namespace EverAccounting\Core;

/**
 * Class Notices
 * @package EverAccounting\Core
 */
class Notices{
	/**
	 * Notices list
	 *
	 * @var array
	 */
	protected $notices = array();

	/**
	 * Notices constructor.
	 */
	public function __construct() {
	}

	/**
	 * @return array
	 */
	function get_admin_notices() {
		return $this->notices;
	}

	/**
	 * @param $admin_notices
	 */
	function set_admin_notices( $admin_notices ) {
		$this->notices = $admin_notices;
	}

	/**
	 * @param $a
	 * @param $b
	 *
	 * @return mixed
	 */
	function notice_priority_sort( $a, $b ) {
		if ( $a['priority'] == $b['priority'] ) {
			return 0;
		}
		return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
	}

	/**
	 * Add notice to UM notices array
	 *
	 * @param string $key
	 * @param array $data
	 * @param int $priority
	 */
	function add_notice( $key, $data, $priority = 10 ) {
		$admin_notices = $this->get_admin_notices();

		if ( empty( $admin_notices[ $key ] ) ) {
			$admin_notices[ $key ] = array_merge( $data, array( 'priority' => $priority ) );
			$this->set_admin_notices( $admin_notices );
		}
	}

	/**
	 * Remove notice from UM notices array
	 *
	 * @param string $key
	 */
	function remove_notice( $key ) {
		$admin_notices = $this->get_admin_notices();

		if ( ! empty( $admin_notices[ $key ] ) ) {
			unset( $admin_notices[ $key ] );
			$this->set_admin_notices( $admin_notices );
		}
	}


	/**
	 * Render all admin notices
	 */
	function render_notices() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$admin_notices = $this->get_admin_notices();

		$hidden = get_option( 'um_hidden_admin_notices', array() );

		uasort( $admin_notices, array( &$this, 'notice_priority_sort' ) );

		foreach ( $admin_notices as $key => $admin_notice ) {
			if ( empty( $hidden ) || ! in_array( $key, $hidden ) ) {
				$this->display_notice( $key );
			}
		}

		/**
		 * UM hook
		 *
		 * @type action
		 * @title um_admin_after_main_notices
		 * @description Insert some content after main admin notices
		 * @change_log
		 * ["Since: 2.0"]
		 * @usage add_action( 'um_admin_after_main_notices', 'function_name', 10 );
		 * @example
		 * <?php
		 * add_action( 'um_admin_after_main_notices', 'my_admin_after_main_notices', 10 );
		 * function my_admin_after_main_notices() {
		 *     // your code here
		 * }
		 * ?>
		 */
		do_action( 'um_admin_after_main_notices' );
	}


	/**
	 * Display single admin notice
	 *
	 * @param string $key
	 * @param bool $echo
	 *
	 * @return void|string
	 */
	function display_notice( $key, $echo = true ) {
		$admin_notices = $this->get_admin_notices();

		if ( empty( $admin_notices[ $key ] ) ) {
			return;
		}

		$notice_data = $admin_notices[ $key ];

		$class = ! empty( $notice_data['class'] ) ? $notice_data['class'] : 'updated';

		$dismissible = ! empty( $admin_notices[ $key ]['dismissible'] );

		ob_start(); ?>

		<div class="<?php echo esc_attr( $class ) ?> um-admin-notice notice <?php echo $dismissible ? 'is-dismissible' : '' ?>" data-key="<?php echo esc_attr( $key ) ?>">
			<?php echo ! empty( $notice_data['message'] ) ? $notice_data['message'] : '' ?>
		</div>

		<?php $notice = ob_get_clean();
		if ( $echo ) {
			echo $notice;
			return;
		} else {
			return $notice;
		}
	}



}
