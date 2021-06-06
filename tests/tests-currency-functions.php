<?php
/**
 * Plugin currency function tests
 *
 * @since 1.1.2
 * @package EverAccounting\Tests
 */

use EverAccounting\Tests\Framework\UnitTestCase;

/**
 * Plugin Currency Tests Functions Class
 *
 * @since 1.1.2
 * @package EverAccounting\Tests
 */
class Tests_Currency_Conversion extends UnitTestCase {
	
	/**
	 * Check argentine Currency
	*/
	public function test_arg_currency() {
		$args = array(
			'name'               => 'Argentine Peso',
			'code'               => 'ARS',
			'rate'               => 1.0000,
			'symbol'             => '$',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		$argentine_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'ARS', $argentine_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'ARS', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'ARS', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'ARS', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('$10,0000', 'ARS' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('$10.98.746,8700', 'ARS' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('$55.68.78.456', 'ARS' )->getAmount(), '556878456' );
		
	}
	
	/**
	 * Check australian Currency
	 */
	public function test_aus_currency() {
		$args = array(
			'name'               => 'Australian Dollar',
			'code'               => 'AUD',
			'rate'               => 1.0000,
			'symbol'             => '$',
			'position'           => 'before',
			'decimal_separator'  => '.',
			'thousand_separator' => ',',
		);
		$australian_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'AUD', $australian_currency->get_code() );

		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10.00', 'AUD', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10,98,746.8700', 'AUD', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55,68,78,456', 'AUD', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('$10.0000', 'AUD' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('$10,98,746.8700', 'AUD' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('$55,68,78,456', 'AUD' )->getAmount(), '556878456' );
	}
	
	/**
	 * Check Unidades de fomento currency
	*/
	public function test_unidades_currency() {
		$args = array(
			'name'               => 'Unidades de fomento',
			'code'               => 'CLF',
			'rate'               => 1.0000,
			'precision'          => 1,
			'subunit'            => 1,
			'symbol'             => 'UF',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		
		$unidades_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'CLF', $unidades_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('187,00', 'CLF', 4 ), '187.0000' );
		$this->assertEquals( eaccounting_sanitize_price('18.46.458,7456', 'CLF', 4 ), '1846458.7456' );
		$this->assertEquals( eaccounting_sanitize_price('55.48.96.369', 'CLF', 4 ), '554896369.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('UF187,00', 'CLF' )->getAmount(), '187' );
		$this->assertEquals( eaccounting_money('UF18.46.458,7456', 'CLF' )->getAmount(), '1846458.7' );
		$this->assertEquals( eaccounting_money('UF55.48.96.369', 'CLF' )->getAmount(), '554896369' );
	}
	
	/**
	 * Check Chilean Peso currency
	 */
	public function test_chilean_currency() {
		$args = array(
			'name'               => 'Chilean Peso',
			'code'               => 'CLP',
			'rate'               => 1.0000,
			'precision'          => 4,
			'subunit'            => 1,
			'symbol'             => '$',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		
		$chilean_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'CLP', $chilean_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'CLP', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'CLP', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'CLP', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('$10,0000', 'CLP' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('$10.98.746,8700', 'CLP' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('$55.68.78.456', 'CLP' )->getAmount(), '556878456' );
	}
	
	/**
	 * Check Columbian Peso currency
	 */
	public function test_columbian_currency() {
		$args = array(
			'name'               => 'Colombian Peso',
			'code'               => 'COP',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => '$',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		
		$columbian_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'COP', $columbian_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'COP', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'COP', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'COP', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('$10,0000', 'COP' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('$10.98.746,8700', 'COP' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('$55.68.78.456', 'COP' )->getAmount(), '556878456' );
	}
	
	/**
	 * Check Costarican Peso currency
	 */
	public function test_costarican_currency() {
		$args = array(
			'name'               => 'Costa Rican Colon',
			'code'               => 'CRC',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => '₡',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		
		$costatican_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'CRC', $costatican_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'CRC', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'CRC', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'CRC', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('₡10,0000', 'CRC' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('₡10.98.746,8700', 'CRC' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('₡55.68.78.456', 'CRC' )->getAmount(), '556878456' );
	}
	
	/**
	 * Check czech Koruna currency
	 */
	public function test_czech_currency() {
		$args = array(
			'name'               => 'Czech Koruna',
			'code'               => 'CZK',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => 'Kč',
			'position'           => 'after',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		$czech_currency = eaccounting_insert_currency( $args );
		$this->assertEquals('CZK', $czech_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'CZK', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'CZK', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'CZK', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('10,0000Kč', 'CZK' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('10.98.746,8700Kč', 'CZK' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('55.68.78.456Kč', 'CZK' )->getAmount(), '556878456' );
	}
	
	/**
	 * Check danish Koruna currency
	 */
	public function test_danish_currency() {
		$args = array(
			'name'               => 'Danish Krone',
			'code'               => 'DKK',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => 'kr',
			'position'           => 'after',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		$danish_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'DKK', $danish_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'DKK', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'DKK', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'DKK', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('10,0000kr', 'DKK' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('10.98.746,8700kr', 'DKK' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('55.68.78.456kr', 'DKK' )->getAmount(), '556878456' );
	}
	
	/**
	 * Check croatian kuna currency
	 */
	public function test_croation_currency() {
		$args = array(
			'name'               => 'Croatian Kuna',
			'code'               => 'HRK',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => 'kn',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		
		$croation_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'HRK', $croation_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'HRK', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'HRK', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'HRK', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('kn10,0000', 'HRK' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('kn10.98.746,8700', 'HRK' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('kn55.68.78.456', 'HRK' )->getAmount(), '556878456' );
		
	}
	
	/**
	 * Check forint currency
	 */
	public function test_forint_currency() {
		$args = array(
			'name'               => 'Forint',
			'code'               => 'HUF',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => 'Ft',
			'position'           => 'after',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		
		$forint_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'HUF', $forint_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'HUF', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'HUF', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'HUF', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('10,0000Ft', 'HUF' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('10.98.746,8700Ft', 'HUF' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('55.68.78.456Ft', 'HUF' )->getAmount(), '556878456' );
	}
	
	/**
	 * Check Rupiah currency
	 */
	public function test_rupiah_currency() {
		$args = array(
			'name'               => 'Rupiah',
			'code'               => 'IDR',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => 'Rp',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		$rupiah_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'IDR', $rupiah_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'IDR', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'IDR', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'IDR', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('Rp10,0000', 'IDR' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('Rp10.98.746,8700', 'IDR' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('Rp55.68.78.456', 'IDR' )->getAmount(), '556878456' );
	}
	
	/**
	 * Check Iceland Krona currency
	 */
	public function test_iceland_currency() {
		$args = array(
			'name'               => 'Iceland Krona',
			'code'               => 'ISK',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => 'kr',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		$iceland_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'ISK', $iceland_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'ISK', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'ISK', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'ISK', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('kr10,0000', 'ISK' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('kr10.98.746,8700', 'ISK' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('kr55.68.78.456', 'ISK' )->getAmount(), '556878456' );
		
	}
	
	/**
	 * Check Russian Ruble currency
	 */
	public function test_russina_currency() {
		$args = array(
			'name'               => 'Russian Ruble',
			'code'               => 'RUB',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => '₽',
			'position'           => 'after',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		$russian_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'RUB', $russian_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'RUB', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'RUB', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'RUB', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('10,0000₽', 'RUB' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('10.98.746,8700₽', 'RUB' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('55.68.78.456₽', 'RUB' )->getAmount(), '556878456' );
	}
	
	/**
	 * Check Uruguayan peso currency
	*/
	public function test_uruguayan_currency() {
		$args = array(
			'name'               => 'Peso Uruguayo',
			'code'               => 'UYU',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => '$',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		$uruguayan_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'UYU', $uruguayan_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'UYU', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'UYU', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'UYU', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('$10,0000', 'UYU' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('$10.98.746,8700', 'UYU' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('$55.68.78.456', 'UYU' )->getAmount(), '556878456' );
	}
	
	/**
	 * Check Bolivian currency
	 */
	public function test_bolivian_currency() {
		$args = array(
			'name'               => 'Bolivar',
			'code'               => 'VEF',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => 'Bs F',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		
		$bolivian_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'VEF', $bolivian_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'VEF', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'VEF', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'VEF', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('Bs F10,0000', 'VEF' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('Bs F10.98.746,8700', 'VEF' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('Bs F55.68.78.456', 'VEF' )->getAmount(), '556878456' );
	}
	
	/**
	 * Check Dong currency
	 */
	public function test_dong_currency() {
		$args = array(
			'name'               => 'Dong',
			'code'               => 'VND',
			'rate'               => 1.0000,
			'precision'          => 2,
			'symbol'             => '₫',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => '.',
		);
		
		$dong_currency = eaccounting_insert_currency( $args );
		$this->assertEquals( 'VND', $dong_currency->get_code() );
		
		//check if the sanitize price is ok
		$this->assertEquals( eaccounting_sanitize_price('10,00', 'VND', 4 ), '10.0000' );
		$this->assertEquals( eaccounting_sanitize_price('10.98.746,8700', 'VND', 4 ), '1098746.8700' );
		$this->assertEquals( eaccounting_sanitize_price('55.68.78.456', 'VND', 4 ), '556878456.0000' );
		
		//check if the frontend price is ok
		$this->assertEquals( eaccounting_money('₫10,0000', 'VND' )->getAmount(), '10.0' );
		$this->assertEquals( eaccounting_money('₫10.98.746,8700', 'VND' )->getAmount(), '1098746.87' );
		$this->assertEquals( eaccounting_money('₫55.68.78.456', 'VND' )->getAmount(), '556878456' );
		
	}
	
	
	
}
