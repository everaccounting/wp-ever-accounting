<?php

class TaxesCest
{
	public function CheckTaxesList( AcceptanceTester $I ) {
		$I->loginAsAdmin();
		$I->amOnPage( 'wp-admin/admin.php?page=eac-misc&tab=taxes' );
		$I->see( 'Taxes', 'h1' );
		$I->see( 'Add New', '.page-title-action' );
		$I->see( 'Import', '.page-title-action' );

		$I->seeElement('.all, .active, .inactive');


	}
}
