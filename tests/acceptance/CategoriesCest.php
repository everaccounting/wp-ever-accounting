<?php

class CategoriesCest {

	// login as admin and check if the categories page is accessible.
	public function checkCategoriesPage( AcceptanceTester $I ) {
		$I->loginAsAdmin();
		$I->amOnPage( '/wp-admin/admin.php?page=eac-misc&tab=categories' );
		$I->see( 'Categories', 'h1' );
	}

	public function addNewCategoryExistingName( AcceptanceTester $I ) {

		$this->checkCategoriesPage($I); // Call the method to check categories page.

		//adding new categories test
		$I->click( 'Add New', '.wp-heading-inline  .page-title-action' );
		$I->see( 'Add Category', 'h1' );
		$I->seeElement( '#eac-category-form' );
		$I->submitForm( '#eac-category-form', array(
			'name'        => 'Test Catcxxccegory',
			'type'        => 'income',
		) );
		$I->see( 'Category with same name and type already exists.', 'p' );
	}

	public function addNewCategory( AcceptanceTester $I ) {
		$I->loginAsAdmin();
		$I->amOnPage( '/wp-admin/admin.php?page=eac-misc&tab=categories' );
		$I->click( 'Add New', '.wp-heading-inline .page-title-action' );
		$I->see( 'Add Category', 'h1' );
		$I->seeElement( '#eac-category-form' );

		$I->submitForm( '#eac-category-form', array(
			'name'        => 'Test3',
		) );
		$I->see( 'Category type is required.', '.notice-error' );


		$I->submitForm( '#eac-category-form', array(
			'name'        => 'newonexx',
			'type'        => 'income',
			'description' => 'this the desxccription',
		) );

		$I->see( 'Category saved successfully.', '.notice-success' );
	}


	public function  UpdateCategories( AcceptanceTester $I, $i = 14 ) {

		$I->loginAsAdmin();
		$I->amOnPage('/wp-admin/admin.php?page=eac-misc&tab=categories&edit='.$i);
		$I->see( 'Edit Category', 'h1' );
		$I->see( 'Actions', '.bkit-card__title' );

		$I->fillField('input[name="name"]', 'new name');
		$I->fillField('textarea[name="description"]', 'new Descriptionsssss');
		$I->selectOption('select[name="type"]', 'item');
		//$I->selectOption('select[name="status"]', 'Inactive');
		// Get the current status
		$currentStatus = $I->grabValueFrom('#status');

		// Change the status to the opposite value
		if ($currentStatus === 'active') {
			$I->selectOption('#status', 'inactive');
		} else {
			$I->selectOption('#status', 'active');
		}

		$I->click('Update', '.column-2','.button button-primary');
		$I->see( 'Category saved successfully.', 'p' );
	}

	public function  DeleteCategory( AcceptanceTester $I, $i = 13 ) {

	 	 $I->loginAsAdmin();
		 $I->amOnPage('/wp-admin/admin.php?page=eac-misc&tab=categories&edit= '.$i);
		 $I->see( 'Edit Category', 'h1' );
		 $I->see( 'Actions', '.bkit-card__title','h2' );
		 $I->click('Delete', '.eac_confirm_delete del');
	}

	/*
	 //DeleteCategories
	public function DeleteAllCategory(AcceptanceTester $I) {
		$this->checkCategoriesPage($I); // Call the method to check categories page.
		$I->click('#cb-select-all-1'); // Click on the "Select All" checkbox.
		$I->selectOption('#bulk-action-selector-top', 'delete'); // Select 'Delete' from the bulk actions dropdown.
		$I->click('#doaction'); // Click on the 'Apply' button.
		$I->see('category(s) deleted successfully.', '.notice-success'); // Check for success message.
	}

	 */

}
