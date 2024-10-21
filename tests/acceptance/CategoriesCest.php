<?php

class CategoriesCest {

	// login as admin and check if the categories page is accessible.
	public function checkCategoriesPage( AcceptanceTester $I ) {
		$I->loginAsAdmin();
		$I->amOnPage( '/wp-admin/admin.php?page=eac-misc&tab=categories' );
		$I->see( 'Categories', 'h1' );
	}

	// test adding a new category.
	public function addNewCategory( AcceptanceTester $I ) {
		$I->loginAsAdmin();
		$I->amOnPage( '/wp-admin/admin.php?page=eac-misc&tab=categories' );
		$I->click( 'Add New', '.wp-heading-inline .page-title-action' );
		$I->see( 'Add Category', 'h1' );
		$I->seeElement( '#eac-category-form' );
		$I->submitForm( '#eac-category-form', array(
			'name'        => 'Test Category',
		) );
		$I->see( 'Category type is required.', '.notice-error' );
		$I->submitForm( '#eac-category-form', array(
			'name'        => 'Test Category',
			'type'        => 'income',
		) );
		$I->see( 'Category saved successfully.', '.notice-success' );
		$I->see( 'Test Category', '#eac-category-form #name' );
	}
}
