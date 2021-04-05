<?php

use _generated\AcceptanceTesterActions;

class ItemCest{

    public function _before( AcceptanceTester $I )
    {
        /**
         * login as admin
         */
        $I->wantTo( 'Check All Possible Outcomes Of Items' );
        $I->loginAsAdmin();
        $I->amOnPluginsPage();
        $I->activatePlugin( 'wp-ever-accounting' );
    }
    /**
     * Test for creating items
     */
    public function CreateItem( AcceptanceTester $I ){ 

        /**
         * Create an item
         */
        $I->amOnPage( 'wp-admin/admin.php?page=ea-items' );
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->wait(1);
        $I->fillField( 'Name', 'Iphone 11 Max Pro' );
        $I->fillField( 'Sale price', '2000');
        $I->fillField( 'Purchase price', '1000');
        $I->fillField( 'Sales Tax (%)', '2');
        $I->fillField( 'Purchase Tax (%)', '2');
        $I->click('Submit');
        $I->wait( 2 );

        /**
         * assert if item is created
         */
        $I->see(' Iphone 11 Max Pro '); 

        /**
         * Edit an item
         */
        $I->click(' Iphone 11 Max Pro ');
        $I->wait(1);
        $I->fillField( 'Name', 'Iphone 11 Max Pro 2' );
        $I->fillField( 'Sale price', '3000');
        $I->fillField( 'Purchase price', '2000');
        $I->fillField( 'Sales Tax (%)', '2');
        $I->fillField( 'Purchase Tax (%)', '2');
        $I->fillField( 'Description', "This is a test by codeception framework" );
        $I->click('Submit');
        $I->wait(2);

        /**
         * assert if item is updated successfully
         */
        $I->see( 'Item updated successfully!');

        
        $I->amOnPage('wp-admin/admin.php?page=ea-items');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->fillField( 'Sale price', '3000');
        $I->fillField( 'Purchase price', '900');
        $I->fillField( 'Sales Tax (%)', '13');
        $I->fillField( 'Purchase Tax (%)', '2');
        $I->click('Submit');
        $I->wait(1);

        /**
         * assert if Name field is empty
         */
        $I->SeeFieldEmpty($I->grabTextFrom('//*[@id="name"]'));
        

        $I->amOnPage('wp-admin/admin.php?page=ea-items');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->fillField('Name', 'Coffee Mug');
        $I->fillField( 'Purchase price', '900');
        $I->fillField( 'Sales Tax (%)', '13');
        $I->fillField( 'Purchase Tax (%)', '2');
        $I->click('Submit');
        $I->wait(2);


        /**
         * assert if Sale price is empty
         */
        $I->see( 'Item Sale Price is required' );
        

    }


}
