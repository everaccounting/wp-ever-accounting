<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Notices {

	/**
	 * @var array
	 */
	protected $notices = array();

	/**
	 * The single instance of the class.
	 *
	 * @var EAccounting_Notices
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main EverAccounting Instance.
	 *
	 * Ensures only one instance of EverAccounting is loaded or can be loaded.
	 *
	 * @return EAccounting_Notices - Main instance.
	 * @since 1.0.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	/**
	 * EAccounting_Notice constructor.
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'print_admin_notices' ), 99 );
	}

	/**
	 * Show success message
	 *
	 * @since 1.0.0
	 * @param $message
	 */
	public function success($message){
		$this->add($message, 'warning');
	}

	/**
	 * Show info message
	 *
	 * @since 1.0.0
	 * @param $message
	 */
	public function info($message){
		$this->add($message, 'info');
	}

	/**
	 * show error message
	 *
	 * @since 1.0.0
	 * @param $message
	 */
	public function error($message){
		$this->add($message, 'error');
	}


	/**
	 * since 1.0.0
	 * @param $message
	 * @param string $type
	 */
	public function add( $message, $type = 'success' ) {
		$message = wp_kses( $message, array(
			'strong' => array(),
			'span' => array( 'class' => true ),
			'i'    => array( 'class' => true ),
			'a'    => array( 'class' => true, 'href' => true ),
		) );

		if ( ! empty( $message ) ) {
			$this->notices[] = array(
				'message' => $message,
				'type'    => $type,
			);
		}
	}

	/**
	 * Show notices
	 *
	 * since 1.0.0
	 * @return string
	 */
	public function print_admin_notices(){

		$html = '';
		if(!empty($this->notices) && is_array($this->notices)){
			foreach ($this->notices as $notice){
				$notice = wp_parse_args($notice, array(
					'message' => '',
					'type' => 'success',
				));

				$html .= sprintf('<div class="notice notice-%s"><p>%s</p></div>',$notice['type'], $notice['message'] );
			}
		}

		echo $html;
	}
}
