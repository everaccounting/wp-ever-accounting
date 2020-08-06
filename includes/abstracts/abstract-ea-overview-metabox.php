<?php
/**
 * Overview Meta Box Base class.
 * Provides a base structure for overview content meta boxes.
 *
 * @class       EAccounting_Transaction
 * @version     1.0.2
 * @package     EverAccounting/Classes
 */
defined( 'ABSPATH' ) || exit();

class EAccounting_Overview_Metabox {
	/**
	 * The ID of the meta box. Must be unique.
	 *
	 * @abstract
	 * @var     string $meta_box_id The ID of the meta box
	 * @since   1.0.2
	 */
	public $meta_box_id;

	/**
	 * The name of the meta box.
	 * This should very briefly describe the contents of the meta box.
	 *
	 * @abstract
	 * @var    string $meta_box_name The name of the meta box
	 * @since   1.0.2
	 */
	public $meta_box_name;

	/**
	 * The EverAccounting screen on which to show the meta box.
	 * Defaults Overview  page.
	 *
	 * The uri of this page is: admin.php?page=eaccounting.
	 *
	 * @access  private
	 * @var array $eaccounting_screen The screen ID of the page on which to display this meta box.
	 * @since   1.0.2
	 */
	private $eaccounting_screen = array(
		'toplevel_page_eaccounting',
	);
	/**
	 * The position in which the meta box will be loaded.
	 * EverAccounting uses custom meta box contexts.
	 * These contexts are listed below.
	 *
	 * 'primary':   Loads in the left column.
	 * 'secondary': Loads in the center column.
	 * 'tertiary':  Loads in the right column.
	 *
	 * All columns will collapse as needed on smaller screens,
	 * as WordPress core meta boxes are in use.
	 *
	 * @var   string $context
	 * @since   1.0.2
	 */
	public $context = 'primary';

	/**
	 * The tooltip content to display above the title on-hover.
	 *
	 * @since 1.0.2
	 *
	 * @var string
	 */
	public $tooltip = '';

	/**
	 * The action on which the meta box will be loaded.
	 * EverAccounting uses custom meta box actions.
	 * These contexts are listed below:
	 *
	 * 'eaccounting_overview_meta_boxes': Loads on the Overview page.
	 *
	 * @var     $action
	 * @since   1.0.2
	 */
	public $action = 'eaccounting_overview_meta_boxes';

	/**
	 * Display callback for the meta box.
	 *
	 * Normal instantiation uses the content() method for display.
	 *
	 * @since  1.0.2
	 * @var    string
	 */
	public $display_callback;

	/**
	 * Additional arguments to pass to the meta box display callback.
	 *
	 * @since  1.0.2
	 * @var    array
	 */
	public $extra_args = array();

	/**
	 * Constructor
	 *
	 * @param array $args {
	 *     Optional. Arguments passed when instantiating standalone meta boxes. If defined,
	 *     all arguments are required.
	 *
	 * @type string $meta_box_id Meta box ID.
	 * @type string $meta_box_name Meta box name label.
	 * @type string $context The position in which the meta box will be loaded.
	 * @type string $action The action upon which the meta box will be loaded.
	 * @type string $display_callback Display callback for the meta box.
	 * }
	 * @return void
	 * @since   1.0.2
	 *
	 */
	public function __construct( $args = array() ) {
		if ( ! empty( $args ) ) {
			$this->maybe_process_args( $args );
		} else {
			$this->display_callback = array( $this, 'content' );

			$this->init();
		}

		add_action( 'add_meta_box', array( $this, 'add_meta_box' ) );
		add_action( $this->action, array( $this, 'add_meta_box' ) );
	}

	/**
	 * Handles passing of arbitrary arguments to override properties normally set
	 * by extending sub-classes.
	 *
	 * @param array $args Meta box arguments.
	 *
	 * @since  1.2.0
	 *
	 */
	private function maybe_process_args( $args ) {

		// Whitelist.
		$required = array(
			'meta_box_id',
			'tooltip',
			'meta_box_name',
			'action',
			'context',
			'display_callback',
			'extra_args',
		);

		foreach ( $args as $arg => $value ) {
			if ( in_array( $arg, $required, true ) ) {
				$this->{$arg} = $value;
			}
		}

	}

	/**
	 * Initializes the meta box.
	 *
	 * Define the meta box name,
	 * and the action on which to hook the meta box here.
	 *
	 * Example:
	 *
	 *    $this->action        = 'eaccounting_overview_meta_boxes';
	 *    $this->meta_box_name = __( 'Name of the meta box', 'wp-ever-accounting' );
	 *
	 * @return  void
	 * @since   1.0.2
	 */
	public function init() {
		die( 'function EAccounting_Overview_Metabox::init() must be overwritten in a sub-class' );
	}

	/**
	 * Adds the meta box
	 *
	 * @return  string A meta box which will display on the specified admin screen.
	 * @uses    add_meta_box
	 * @since   1.0.2
	 */
	public function add_meta_box() {

		if ( ! empty( $this->tooltip ) ) {
			$screen = get_current_screen();

			if ( $screen instanceof \WP_Screen ) {

				add_filter( "postbox_classes_{$screen->base}_{$this->meta_box_id}", function ( $classes ) {
					$classes[] = 'has-tooltip';

					return $classes;
				} );

			}
		}

		add_meta_box(
			$this->meta_box_id,
			$this->meta_box_name,
			array( $this, 'get_content' ),
			$this->eaccounting_screen,
			$this->context,
			'default',
			$this->extra_args
		);
	}

	/**
	 * Gets the content set in $this->content().
	 *
	 * @return mixed string The content of the meta box.
	 * @since  1.0.2
	 */
	public function get_content() {
		$content = '';

		if ( is_callable( $this->display_callback ) ) {
			$content = call_user_func( $this->display_callback, $this->extra_args );
		}

		/**
		 * Filter the title tag content for an admin page.
		 *
		 * The dynamic portion of the hook name, $meta_box_id, refers to the ID of the meta box.
		 *
		 * @param string $content The content of the meta box, set in $this->content().
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'eaccounting_meta_box_' . $this->meta_box_id, $content );
	}

	/**
	 * Defines the meta box content, as well as a
	 * filter by which the content may be adjusted.
	 *
	 * Use this method in your child class to define
	 * the content of your meta box.
	 *
	 * For example, given a $meta_box_id value of 'my-metabox-id',
	 * the filter would be: eaccounting_meta_box_my-meta-box-id.
	 *
	 * @return mixed string The content of the meta box
	 * @since  1.0.2
	 */
	public function content() {
		die( 'function EAccounting_Overview_Metabox::content() must be overwritten in a sub-class' );
	}
}
