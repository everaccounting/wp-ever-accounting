<?php 

class CategoriesCest
{
    public function _before(AcceptanceTester $I)
    {
        /**
         * login as admin
         */
        $I->wantTo( 'Check All Possible Outcomes Of Customers' );
        $I->loginAsAdmin();
        $I->amOnPluginsPage();
        $I->activatePlugin( 'wp-ever-accounting' );
    }

    // tests
    public function Categories(AcceptanceTester $I)
    {
        /**
         * create category
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-settings&tab=general');
        $I->click( '//*[@id="wpbody-content"]/div[2]/nav/a[3]' );
        $I->click('//*[@id="wpbody-content"]/div[2]/a[1]');
        $I->fillField('Name', 'Automobiles');
        $I->selectOption('Type', 'Other');
        $I->click('//*[@id="color"]');
        $I->wait(2);
        $I->click('//*[@id="ea-category-form"]/div/div[2]/div/div[3]/div[1]');
        $I->wait(2);
        $I->click('Submit');
        $I->wait(2);

        /**
         * assert if item is created
         */
        $I->see('Automobiles');

        /**
         * edit category
         */
        $I->click('//*[@id="the-list"]/tr[1]/td[1]/a');
        $I->wait(2);

        /**
         * assert if edit works
         */
        $I->see('Update Category');

        /**
         * assert if Category Name field is empty
         */
        $I->click( '//*[@id="wpbody-content"]/div[2]/nav/a[3]' );
        $I->click('//*[@id="wpbody-content"]/div[2]/a[1]');
        $I->selectOption('Type', 'Other');
        $I->click('//*[@id="color"]');
        $I->wait(2);
        $I->click('//*[@id="ea-category-form"]/div/div[2]/div/div[3]/div[1]');
        $I->wait(2);
        $I->click('Submit');
        $I->wait(1);
        
        $I->expect('Please fill out this field.');


    }
}
