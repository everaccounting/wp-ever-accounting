<?php

class ItemCest {


	// login as admin and check if the categories page is accessible.
	public function checkItemPage( AcceptanceTester $I ) {
		$I->loginAsAdmin();
		$I->amOnPage( '/wp-admin/admin.php?page=eac-items' );
		$I->see( 'Items', 'h1' );
	}

	public function AddnnewItem( AcceptanceTester $I ) {
		$this->checkItemPage($I); // Call the method to check categories page.
		$I->see( 'Items', 'h1' );
		$I->click( 'Add New', '.page-title-action' );
		$I->see( 'Add Item', 'h1' );

	}
}
