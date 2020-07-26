<?php

class EA_UnitTestCase extends WP_UnitTestCase {
	public static function wpSetUpBeforeClass() {
		EAccounting_Install::install();
	}
}
