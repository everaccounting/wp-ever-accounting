<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Notices {
	/**
	 * @var array
	 */
	protected $notices = array();

	/**
	 * @var string
	 */
	protected $notice_key;

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
	public static function instance($notice_key) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self($notice_key);
		}

		return self::$_instance;
	}


	/**
	 * EAccounting_Notice constructor.
	 *
	 * @param string $notice_key
	 */
	public function __construct( $notice_key ) {
		$this->notice_key = $notice_key;
		add_action( 'shutdown', array( $this, 'save_notices' ) );
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
	public function show(){
		$notices = $this->get_notices();
		$html = '';
		if(!empty($notices) && is_array($notices)){
			foreach ($notices as $notice){
				$notice = wp_parse_args($notice, array(
					'message' => '',
					'type' => 'success',
				));
				$html .= sprintf('<div class="notice notice-%s"><p>%s</p></div>',$notice['type'], $notice['message'] );
			}
		}

		$this->clear();

		return $html;
	}

	/**
	 * since 1.0.0
	 */
	public function save_notices() {
		if ( ! empty( $this->notices ) && is_array( $this->notices ) ) {
			update_option( $this->notice_key, serialize( $this->notices ) );
		}
	}

	/**
	 * Get notices
	 * since 1.0.0
	 * @return array
	 */
	protected function get_notices() {
		return maybe_unserialize( get_option( $this->notice_key, array() ) );
	}

	/**
	 * Clear
	 * since 1.0.0
	 */
	protected function clear(){
		update_option( $this->notice_key, serialize( $this->notices ) );
	}


}
