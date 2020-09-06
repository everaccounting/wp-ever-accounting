<?php
/**
 * Setup Wizard Class
 *
 * Takes new users through some basic steps to setup the environment.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @since       1.0.2
 */

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit();

/**
 * Class Setup_Wizard
 * @since   1.0.2
 *
 * @package EverAccounting\Admin
 */
class Setup_Wizard{

	/**
	 * Current step
	 *
	 * @var string
	 */
	private $step = '';

	/**
	 * Steps for the setup wizard
	 *
	 * @var array
	 */
	private $steps = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		if ( apply_filters( 'eaccounting_enable_setup_wizard', true ) && current_user_can( 'manage_eaccounting' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
//			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'ea-setup', '' );
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'ea-setup' !== $_GET['page'] ) {
			return;
		}
		$default_steps = array(
			'company' => array(
				'name'    => __( 'Company setup', 'woocommerce' ),
				'view'    => array( $this, 'wc_setup_new_onboarding' ),
				'handler' => array( $this, 'wc_setup_new_onboarding_save' ),
			),
			'currency' => array(
				'name'    => __( 'Currency setup', 'woocommerce' ),
				'view'    => array( $this, 'wc_setup_new_onboarding' ),
				'handler' => array( $this, 'wc_setup_new_onboarding_save' ),
			),
			'category' => array(
				'name'    => __( 'Category setup', 'woocommerce' ),
				'view'    => array( $this, 'wc_setup_new_onboarding' ),
				'handler' => array( $this, 'wc_setup_new_onboarding_save' ),
			),
			'next_steps'     => array(
				'name'    => __( 'Ready!', 'woocommerce' ),
				'view'    => array( $this, 'wc_setup_ready' ),
				'handler' => '',
			),
		);


		$this->steps = apply_filters( 'eaccounting_setup_wizard_steps', $default_steps );
		$this->step  = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) ); // WPCS: CSRF ok, input var ok.

		// @codingStandardsIgnoreStart
		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}
		// @codingStandardsIgnoreEnd

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}


	/**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		// same as default WP from wp-admin/admin-header.php.
		$wp_version_class = 'branch-' . str_replace( array( '.', ',' ), '-', floatval( get_bloginfo( 'version' ) ) );

		set_current_screen();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'Ever Accounting &rsaquo; Setup Wizard', 'wp-ever-accounting' ); ?></title>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php wp_print_scripts( 'ea-setup' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="ea-setup wp-core-ui <?php echo esc_attr( 'ea-setup-step__' . $this->step ); ?> <?php echo esc_attr( $wp_version_class ); ?>">
		<h1 class="ea-logo"><a href="https://woocommerce.com/"><img src="<?php echo esc_url( eaccounting()->plugin_url('/assets/images/woocommerce_logo.png') ); ?>" alt="<?php esc_attr_e( 'Ever Accounting', 'wp-ever-accounting' ); ?>" /></a></h1>
		<?php
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		$current_step = $this->step;
		?>
		<?php do_action( 'eaccounting_setup_footer' ); ?>
		</body>
		</html>
		<?php
	}

	/**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$output_steps      = $this->steps;
		?>
		<ol class="ea-setup-steps">
			<?php
			foreach ( $output_steps as $step_key => $step ) {
				$is_completed = array_search( $this->step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true );

				if ( $step_key === $this->step ) {
					?>
					<li class="active"><?php echo esc_html( $step['name'] ); ?></li>
					<?php
				} elseif ( $is_completed ) {
					?>
					<li class="done">
						<a href="<?php echo esc_url( add_query_arg( 'step', $step_key, remove_query_arg( 'activate_error' ) ) ); ?>"><?php echo esc_html( $step['name'] ); ?></a>
					</li>
					<?php
				} else {
					?>
					<li><?php echo esc_html( $step['name'] ); ?></li>
					<?php
				}
			}
			?>
		</ol>
		<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		echo '<div class="ea-setup-content">';
		if ( ! empty( $this->steps[ $this->step ]['view'] ) ) {
			call_user_func( $this->steps[ $this->step ]['view'], $this );
		}
		echo '</div>';
	}

}

new Setup_Wizard();
