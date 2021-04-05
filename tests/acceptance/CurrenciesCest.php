<?php 

class CurrenciesCest
{
    public function _before(AcceptanceTester $I)
    {
        /**
         * login as admin
         */
        $I->wantTo( 'Check All Possible Outcomes Of Currencies' );
        $I->loginAsAdmin();
        $I->amOnPluginsPage();
        $I->activatePlugin( 'wp-ever-accounting' );
    }

    // tests
    public function Currencies(AcceptanceTester $I)
    {
        /**
         * currency create
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-settings&tab=general');
        $I->click('//*[@id="wpbody-content"]/div[2]/nav/a[2]');
        $I->click('//*[@id="wpbody-content"]/div[2]/a[1]');
        $I->selectOption( 'Currency Code', 'BDT' );
        $I->fillField( 'Currency Rate', '2.00');
        $I->fillField( 'Precision', '2' );
        $I->click('Submit');   
        $I->wait(2);

        /**
         * assert if currency is created
         */
        $I->see('BDT');

        /**
         * edit currency
         */
        $I->click('//*[@id="the-list"]/tr[3]/td[1]/a');

        /**
         * assert if currency can be updated
         */
        $I->see('Update Currency');
        

        /**
         * assert if Currency Name is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-settings&tab=currencies');
        $I->click('//*[@id="wpbody-content"]/div[2]/a[1]');
        $I->selectOption( 'Currency Code', 'BDT' );
        $I->fillField( 'Currency Rate', '2.00');
        $I->fillField( 'Precision', '2' );
        $I->fillField('Name', '');
        $I->click('Submit');
        $I->SeeFieldEmpty( $I->grabTextFrom('//*[@id="name"]') );

        /**
         * assert if Precision Field is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-settings&tab=currencies');
        $I->click('//*[@id="wpbody-content"]/div[2]/a[1]');
        $I->selectOption( 'Currency Code', 'BDT' );
        $I->fillField( 'Currency Rate', '2.00');
        $I->fillField( 'Precision', '' );
        $I->click('Submit');
        $I->SeeFieldEmpty( $I->grabTextFrom('//*[@id="precision"]') );

        /**
         * assert if Currency Rate is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-settings&tab=currencies');
        $I->click('//*[@id="wpbody-content"]/div[2]/a[1]');
        $I->selectOption( 'Currency Code', 'BDT' );
        $I->fillField( 'Precision', '2' );
        $I->fillField( 'Currency Rate', '');
        $I->click('Submit'); 
        $I->SeeFieldEmpty( $I->grabTextFrom('//*[@id="rate"]') );  
        $I->wait(10);
        
    }
}
