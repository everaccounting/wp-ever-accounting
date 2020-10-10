<?php
/**
 * Setup Wizard Class
 *
 * Takes new users through some basic steps to setup the environment.
 *
 * @since       1.0.2
 * @subpackage  Admin
 * @package     EverAccounting
 */

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit();

/**
 * Class Setup_Wizard
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Admin
 */
class Setup_Wizard {

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
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'ea-setup', '' );
	}

	public function enqueue_scripts() {
		$version = eaccounting()->get_version();
		$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'ea-admin', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-admin' . $suffix . '.js' ), array( 'jquery' ), $version );
		wp_register_style( 'ea-admin-styles', eaccounting()->plugin_url() . '/assets/css/admin.css', array(), $version );

		wp_register_style( 'ea-setup', eaccounting()->plugin_url() . '/assets/css/setup.css', array( 'dashicons', 'install' ), $version );
		// Add RTL support for admin styles.
		wp_style_add_data( 'ea-setup', 'rtl', 'replace' );

		wp_register_script( 'ea-setup', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-setup' . $suffix . '.js' ), array( 'jquery' ), $version );

		wp_enqueue_style( 'ea-admin-styles' );
		wp_enqueue_style( 'ea-setup' );
		wp_enqueue_script( 'ea-setup' );
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'ea-setup' !== $_GET['page'] ) {
			return;
		}
		$default_steps = array(
				'introduction' => array(
						'name'    => __( 'Introduction', 'wp-ever-accounting' ),
						'view'    => array( $this, 'setup_introduction' ),
						'handler' => ''
				),
				'company'      => array(
						'name'    => __( 'Company setup', 'wp-ever-accounting' ),
						'view'    => array( $this, 'company_settings' ),
						'handler' => array( $this, 'company_settings_save' ),
				),
				'currency'     => array(
						'name'    => __( 'Currency setup', 'wp-ever-accounting' ),
						'view'    => array( $this, 'wc_setup_new_onboarding' ),
						'handler' => array( $this, 'wc_setup_new_onboarding_save' ),
				),
				'category'     => array(
						'name'    => __( 'Category setup', 'wp-ever-accounting' ),
						'view'    => array( $this, 'wc_setup_new_onboarding' ),
						'handler' => array( $this, 'wc_setup_new_onboarding_save' ),
				),
				'next_steps'   => array(
						'name'    => __( 'Ready!', 'wp-ever-accounting' ),
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
	 * Get the URL for the next step's screen.
	 *
	 * @since 1.0.2
	 *
	 * @param string $step slug (default: current step).
	 *
	 * @return string       URL for next step if a next step exists.
	 *                      Admin URL if it's the last step.
	 *                      Empty string on failure.
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );
		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys, true );
		if ( false === $step_index ) {
			return '';
		}

		return add_query_arg( 'step', $keys[ $step_index + 1 ], remove_query_arg( 'activate_error' ) );
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
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<title><?php esc_html_e( 'Ever Accounting &rsaquo; Setup Wizard', 'wp-ever-accounting' ); ?></title>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="ea-setup wp-core-ui <?php echo esc_attr( 'ea-setup-step__' . $this->step ); ?> <?php echo esc_attr( $wp_version_class ); ?>">
		<h1 class="ea-logo"><a href="https://wpeveraccounting.com/"><img src="<?php echo esc_url( eaccounting()->plugin_url( '/assets/images/logo.svg' ) ); ?>" alt="<?php esc_attr_e( 'Ever Accounting', 'wp-ever-accounting' ); ?>"/></a></h1>
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
		$output_steps = $this->steps;
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


	public function setup_introduction() {
		?>
		<h1><?php _e( 'Welcome to WP Ever Accounting!', 'wp-ever-accounting' ); ?></h1>
		<p><?php _e( 'Thank you for choosing WP Ever Accounting to manage your accounting! This quick setup wizard will help you configure the basic settings.', 'wp-ever-accounting' ); ?></p>
		<p class="ea-setup-actions step">
			<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
			   class="button-primary button button-large button-next"><?php _e( 'Let\'s Go!', 'wp-ever-accounting' ); ?></a>
		</p>
		<?php
	}


	public function company_settings() {
		?>
		<h1><?php _e( 'Company Setup', 'wp-ever-accounting' ); ?></h1>
		<form method="post">
			<?php
			eaccounting_text_input( array(
					'label'    => __( 'Company Name', 'wp-ever-accounting' ),
					'name'     => 'company_name',
					'required' => true
			) );
			eaccounting_text_input( array(
					'label'    => __( 'Company Email', 'wp-ever-accounting' ),
					'name'     => 'company_email',
					'default'  => get_option( 'admin_email' ),
					'required' => true,
					'type'     => 'email',
			) );

			eaccounting_textarea( array(
					'label'    => __( 'Company Address', 'wp-ever-accounting' ),
					'name'     => 'company_address',
			) );
			eaccounting_country_dropdown( array(
					'label'    => __( 'Country', 'wp-ever-accounting' ),
					'name'     => 'company_country',
					'required' => true,
			) );
			?>

			<p class="ea-setup-actions step">
				<input type="submit"
					   class="button-primary button button-large button-next"
					   value="<?php esc_attr_e('Continue', 'wp-ever-accounting'); ?>" name="save_step"/>
				<?php wp_nonce_field('company-setup'); ?>

			</p>
		</form>
		<?php
	}
}

new Setup_Wizard();
