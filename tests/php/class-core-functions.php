<?php
/**
 * Plugin core function tests
 *
 * @since 1.0.0
 * @package EverAccounting\Tests
 */

/**
 * Plugin Core Functions Class
 *
 * @since 1.0.0
 * @package EverAccounting\Tests
 */
class Tests_Core_Functions extends \WP_UnitTestCase {
	public function test_get_options() {
		$company_name = wp_hash_password( 10 );
		eaccounting_update_option( 'company_name', $company_name );
		$value = eaccounting_get_option( 'company_name' );
		$this->assertEquals( $value, $company_name );
		$this->assertEquals( eaccounting_get_option( 'nothing_exist', 'nothing_exist' ), 'nothing_exist' );
	}

	public function test_update_option() {
		$company_name = wp_hash_password( 10 );
		eaccounting_update_option( 'company_name', $company_name );
		$value = eaccounting_get_option( 'company_name' );
		$this->assertEquals( $value, $company_name );
		$company_name_new = wp_hash_password( 10 );
		eaccounting_update_option( 'company_name', $company_name_new );
		$this->assertEquals( eaccounting_get_option( 'company_name' ), $company_name_new );
	}

	public function financial_start() {
		$year           = date( 'Y' );
		$financial_date = eaccounting_get_financial_start();
		$this->assertEquals( $financial_date, $year . '-01-01' );
		$this->assertEquals( eaccounting_get_financial_start( $year - 1 ), ( $year - 1 ) . '-01-01' );
		$this->assertEquals( eaccounting_get_financial_start( $year + 1 ), ( $year + 1 ) . '-01-01' );
		eaccounting_update_option('financial_year_start', '01-07');
		$this->assertEquals( $financial_date, $year . '-01-07' );
		$this->assertEquals( eaccounting_get_financial_start( $year - 1 ), ( $year - 1 ) . '-01-07' );
		$this->assertEquals( eaccounting_get_financial_start( $year + 1 ), ( $year + 1 ) . '-01-07' );
	}
}
