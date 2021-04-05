<?php 

class VendorsCest
{
    public function _before(AcceptanceTester $I)
    {
        /**
         * login as admin
         */
        $I->wantTo( 'Check All Possible Outcomes Of Vendors' );
        $I->loginAsAdmin();
        $I->amOnPluginsPage();
        $I->activatePlugin( 'wp-ever-accounting' );
    }

    // tests
    public function Vendors(AcceptanceTester $I)
    {
        $I->amOnPage( 'wp-admin/admin.php?page=ea-expenses&tab=bills' );
        $I->click('Vendors');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        
        $I->fillField('Name', 'Roman Biswas');
        $I->selectOption( 'Currency', 'Euro' );
        $I->fillField( 'Company', 'Infosys' );
        $I->fillField( 'Email', 'infosys@gmail.com' );
        $I->fillField( 'Phone', '01906759888' );
        $I->fillField( 'VAT Number', 'vat-02' );
        $I->fillField( 'Website', 'www.infosys.com' );
        $I->click('//*[@id="birth_date"]');
        $I->click(22);
        $I->fillField('Street', 'Moakhali Dohs');
        $I->fillField('City', 'Dhaka');
        $I->fillField('State', 'Dhaka Sadar');
        $I->fillField('Postcode', '20022001');
        $I->selectOption('Country', 'Belarus');
        $I->click('Submit');
        $I->wait(2);

        /**
         * assert if vendor is created
         */
        $I->see('Roman Biswas');

         /**
         * edit vendor
         */
        $I->click('//*[@id="the-list"]/tr/td[2]/a/strong');
        $I->wait(2);
        $I->click('//*[@id="wpbody-content"]/div[2]/div/div[2]/div[2]/div/div[1]/a');

        /**
         * assert if edit works
         */
        $I->see('Update Vendor');
        $I->wait(2);

        /**
         * assert if Name field is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-expenses&tab=vendors');
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
