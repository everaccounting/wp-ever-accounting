<?php

class TaxesCest


{

	public function CheckTaxesList( AcceptanceTester $I ) {
		//Login
		$I->loginAsAdmin();
		$I->amOnPage( 'wp-admin/admin.php?page=eac-misc&tab=taxes' );

		//Check the Page
		$I->see( 'Taxes', 'h1' );
		$I->see( 'Add New', '.page-title-action' );
		$I->see( 'Import', '.page-title-action' );
		$I->seeElement('.all, .active, .inactive');

	}

/*
	//loop for  save randomly multiple time

	public function runAddNewTaxesMultipleTimes(AcceptanceTester $I) {
		for ($i = 0; $i < 5; $i++) {
			$this->addNewTaxes($I, $i);
		}
	}
 */
	public function addNewTaxes( AcceptanceTester $I, $i = 555 ) {

		$this->CheckTaxesList($I); // Call the method to check categories page.

		//Go to Add New Form
		$I->click( 'Add New', '.wp-heading-inline .page-title-action' );
		$I->see( 'Add Tax', 'h1' );
		$I->seeElement( '#eac-tax-form' );

		$I->submitForm( '#eac-tax-form', array(
			'name' => 'TestName' . $i,
			'rate' => rand(1, 10), // Generate a random rate
			'is_compound' => ($i % 2 == 0) ? 'yes' : 'no', // Alternate between 'yes' and 'no'
			'description' => 'Test Description ' . $i,
			'status' => ($i % 2 == 0) ? 'active' : 'inactive',
		) );

		$I->see( 'Tax saved successfully.', 'p' );

	}

	public function  UpdateTaxes( AcceptanceTester $I, $i = 203 ) {

		$I->loginAsAdmin();
		$I->amOnPage('/wp-admin/admin.php?page=eac-misc&tab=taxes&edit='.$i);
		$I->see( 'Edit Tax', 'h1' );
		$I->see( 'Actions', '.bkit-card__title' );

		$I->fillField('input[name="name"]', 'new values'.$i+5);
		$I->fillField('input[name="rate"]', $i-100);
		$I->fillField('textarea[name="description"]', 'new Description'.$i+5);
		$I->selectOption('select[name="is_compound"]', 'No');
		$I->selectOption('select[name="status"]', 'Inactive');
		// Get the current status
		$currentStatus = $I->grabValueFrom('#status');

		// Change the status to the opposite value
		if ($currentStatus === 'active') {
			$I->selectOption('#status', 'inactive');
		} else {
			$I->selectOption('#status', 'active');
		}

		$I->click('Update Tax', '.column-2','.button button-primary');
		$I->see( 'Tax saved successfully.', 'p' );
	}

	public function  DeleteTaxes( AcceptanceTester $I, $i = 176 ) {

		$I->loginAsAdmin();
		$I->amOnPage('/wp-admin/admin.php?page=eac-misc&tab=taxes&edit='.$i);
		$I->see( 'Edit Tax', 'h1' );
		$I->see( 'Actions', '.bkit-card__title' );
		$I->click('Delete', '.column-2','.eac_confirm_delete del');

	}

}


