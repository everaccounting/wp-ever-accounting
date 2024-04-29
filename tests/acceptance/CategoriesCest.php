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
			'name'        => 'Test2',
		) );
		$I->see( 'Category type is required.', '.notice-error' );
		$I->submitForm( '#eac-category-form', array(
			'name'        => 'Test2',
			'type'        => 'income',
		) );
		$I->see( 'Category saved successfully.', '.notice-success' );
	}


	/*
	 //DeleteCategories
	public function deleteCategory(AcceptanceTester $I) {
		$this->checkCategoriesPage($I); // Call the method to check categories page.
		$I->click('#cb-select-all-1'); // Click on the "Select All" checkbox.
		$I->selectOption('#bulk-action-selector-top', 'delete'); // Select 'Delete' from the bulk actions dropdown.
		$I->click('#doaction'); // Click on the 'Apply' button.
		$I->see('category(s) deleted successfully.', '.notice-success'); // Check for success message.
	}

	 */

}
