<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class SettingsPage.
 *
 * @since   1.0.0
 * @package EverAccounting
 */
abstract class SettingsTab {
	/**
	 * Setting tab id.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Setting tab label.
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'ever_accounting_settings_tabs_array', array( $this, 'add_settings_tab' ), 0 );
		add_action( 'ever_accounting_settings_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'ever_accounting_settings_tab_' . $this->id, array( $this, 'output' ) );
		add_action( 'ever_accounting_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings page ID.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get settings page label.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Add this page to settings.
	 *
	 * @param array $pages The settings array where we'll add ourselves.
	 *
	 * @return mixed
	 */
	public function add_settings_tab( $pages ) {
		$pages[ $this->id ] = $this->label;

		return $pages;
	}


	/**
	 * Get settings array.
	 *
	 * The strategy for getting the settings is as follows:
	 *
	 * - If a method named 'get_settings_for_{section_id}_section' exists in the class
	 *   it will be invoked (for the default '' section, the method name is 'get_settings_for_default_section').
	 *   Derived classes can implement these methods as required.
	 *
	 * - Otherwise, 'get_settings_for_section_core' will be invoked. Derived classes can override it
	 *   as an alternative to implementing 'get_settings_for_{section_id}_section' methods.
	 *
	 * @param string $section_id The id of the section to return settings for, an empty string for the default section.
	 *
	 * @return array Settings array, each item being an associative array representing a setting.
	 */
	final public function get_settings_for_section( $section_id ) {
		if ( empty( $section_id ) ) {
			$method_name = 'get_settings_for_default_section';
		} else {
			$method_name = "get_settings_for_{$section_id}_section";
		}

		if ( method_exists( $this, $method_name ) ) {
			$settings = $this->$method_name();
		} else {
			$settings = $this->get_settings_for_section_core( $section_id );
		}

		return apply_filters( 'ever_accounting_get_settings_' . $this->id, $settings, $section_id );
	}

	/**
	 * Get the settings for a given section.
	 * This method is invoked from 'get_settings_for_section' when no 'get_settings_for_{current_section}_section'
	 * method exists in the class.
	 *
	 * When overriding, note that the 'ever_accounting_get_settings_' filter must NOT be triggered,
	 * as this is already done by 'get_settings_for_section'.
	 *
	 * @param string $section_id The section name to get the settings for.
	 *
	 * @return array Settings array, each item being an associative array representing a setting.
	 */
	protected function get_settings_for_section_core( $section_id ) {
		return array();
	}

	/**
	 * Get all sections for this page, both the own ones and the ones defined via filters.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = $this->get_own_sections();
		return apply_filters( 'ever_accounting_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get own sections for this page.
	 * Derived classes should override this method if they define sections.
	 * There should always be one default section with an empty string as identifier.
	 *
	 * Example:
	 * return array(
	 *   ''        => __( 'General', 'wp-ever-accounting' ),
	 *   'foobars' => __( 'Foos & Bars', 'wp-ever-accounting' ),
	 * );
	 *
	 * @return array An associative array where keys are section identifiers and the values are translated section names.
	 */
	protected function get_own_sections() {
		return array( '' => __( 'General', 'wp-ever-accounting' ) );
	}

	/**
	 * Output sections.
	 */
	public function output_sections() {
		global $current_section;

		$sections = $this->get_sections();

		if ( empty( $sections ) || 1 === count( $sections ) ) {
			return;
		}

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			$url       = admin_url( 'admin.php?page=eac-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) );
			$class     = ( $current_section === $id ? 'current' : '' );
			$separator = ( end( $array_keys ) === $id ? '' : '|' );
			$text      = esc_html( $label );
			echo "<li><a href='$url' class='$class'>$text</a> $separator </li>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</ul><br class="clear" />';
	}

	/**
	 * Output the HTML for the settings.
	 */
	public function output() {
		global $current_tab, $current_section;

		// We can't use "get_settings_for_section" here
		// for compatibility with derived classes overriding "get_settings".
		$settings = $this->get_settings_for_section( $current_section );
		?>
		<?php if ( ! empty( $settings ) ) : ?>
			<form method="post" id="mainform" action="" enctype="multipart/form-data">
				<?php Settings::output_fields( $settings ); ?>
				<?php wp_nonce_field( 'ever-accounting-settings' ); ?>
				<?php if ( apply_filters( 'ever_accounting_settings_save_button_' . $current_tab, true, $current_section ) ) : ?>
					<p class="submit"><button name="save" class="button-primary eac-save-button" type="submit" value="<?php esc_attr_e( 'Save changes', 'wp-ever-accounting' ); ?>"><?php esc_html_e( 'Save changes', 'wp-ever-accounting' ); ?></button></p>
				<?php endif; ?>
			</form>
		<?php endif; ?>

		<?php
	}

	/**
	 * Save settings and trigger the 'ever_accounting_update_options_'.id action.
	 */
	public function save() {
		$this->save_settings_for_current_section();
		$this->do_update_options_action();
	}

	/**
	 * Save settings for current section.
	 */
	protected function save_settings_for_current_section() {
		global $current_section;

		// We can't use "get_settings_for_section" here
		// for compatibility with derived classes overriding "get_settings".
		$settings = $this->get_settings_for_section( $current_section );
		Settings::save_fields( $settings );
	}

	/**
	 * Trigger the 'ever_accounting_update_options_'.id action.
	 *
	 * @param string $section_id Section to trigger the action for, or null for current section.
	 */
	protected function do_update_options_action( $section_id = null ) {
		global $current_section;

		if ( is_null( $section_id ) ) {
			$section_id = $current_section;
		}

		if ( $section_id ) {
			do_action( 'ever_accounting_update_options_' . $this->id . '_' . $section_id );
		}
	}
}
