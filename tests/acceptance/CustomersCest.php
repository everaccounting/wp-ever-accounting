<?php 

class CustomersCest
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
    public function Customers(AcceptanceTester $I)
    {
        /**
         * create customer
         */
        $I->amOnPage( 'wp-admin/admin.php?page=ea-sales' );
        $I->click('Customers');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        
        $I->fillField('Name', 'Robert Downey Jr');
        $I->selectOption( 'Currency', 'Pound Sterling' );
        $I->fillField( 'Company', 'Byteever' );
        $I->fillField( 'Email', 'byteever@gmail.com' );
        $I->fillField( 'Phone', '01906759899' );
        $I->fillField( 'VAT Number', 'vat-01' );
        $I->fillField( 'Website', 'www.byteever.com' );
        $I->click('//*[@id="birth_date"]');
        $I->click(22);
        $I->fillField('Street', 'Mirpur Dohs');
        $I->fillField('City', 'Dhaka');
        $I->fillField('State', 'Dhaka Sadar');
        $I->fillField('Postcode', '20022002');
        $I->selectOption('Country', 'Belgium');
        $I->click('Submit');
        $I->wait(2);

        /**
         * assert if customer is created
         */
        $I->see('Robert Downey Jr');

        /**
         * edit customer
         */
        $I->click('//*[@id="the-list"]/tr/td[2]/a/strong');
        $I->wait(2);
        $I->click('//*[@id="wpbody-content"]/div[2]/div/div[2]/div[2]/div/div[1]/a');

        /**
         * assert if edit works
         */
        $I->see('Update Customer');
        $I->wait(2);

        /**
         * assert if Name field is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-sales&tab=customers');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->fillField( 'Company', 'LLC' );
        $I->fillField('Website', 'www.xyz.com');
        $I->click('Submit');
        $I->wait(2);
        
        /**
         * assert if name field is empty
         */
        $I->SeeFieldEmpty( $I->grabTextFrom('//*[@id="name"]') );
    }
}
