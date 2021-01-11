<?php
/**
 * Admin Extensions Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Tools
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

class EverAccounting_Admin_Extensions {

	/**
	 * EverAccounting_Admin_Extensions constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 999 );
	}

	/**
	 * Registers the extensions page.
	 *
	 */
	public function register_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Extensions', 'wp-ever-accounting' ),
			__( 'Extensions', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-extensions',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render page.
	 *
	 * @since 1.1.0
	 */
	public function render_page() {
		$extensions = $this->get_extensions();
		var_dump($extensions);
		?>
		<div class="wrap">
			<h2><?php esc_html_e('Extensions', 'wp-ever-accounting');?></h2>
			<?php foreach ($extensions as $extension):?>
			<div class="ea-extension">
				<h3 class="ea-extension__title"></h3>
				<?php //var_dump($extension);?>
			</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	public function get_extensions(){
		$cache = false;//get_transient( 'wpeveraccounting_extensions_feed' );

		if ( false === $cache ) {
			$url = 'https://wpeveraccounting.com/edd-api/products/';

			$feed = wp_remote_get( esc_url_raw( $url ), array( 'sslverify' => false ) );

			if ( ! is_wp_error( $feed ) ) {
				if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
					$cache =  wp_remote_retrieve_body( $feed )->products ;
					set_transient( 'wpeveraccounting_extensions_feed', $cache, 3600 );
				}
			} else {
				$cache = '<div class="error"><p>' . __( 'There was an error retrieving the extensions list from the server. Please try again later.', 'wp-ever-accounting' ) . '</div>';
			}
		}

		return json_decode($cache);
	}

}
return new EverAccounting_Admin_Extensions();
