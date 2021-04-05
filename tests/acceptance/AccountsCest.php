<?php 

class AccountsCest
{
    public function _before(AcceptanceTester $I)
    {
        /**
         * login as admin
         */
        $I->wantTo( 'Check All Possible Outcomes Of Accounts' );
        $I->loginAsAdmin();
        $I->amOnPluginsPage();
        $I->activatePlugin( 'wp-ever-accounting' );
    }


    // tests
    public function Accounts(AcceptanceTester $I) 
    {
        /**
         * assert if the Account is created
         */
        $I->amOnPage( 'wp-admin/admin.php?page=ea-banking' );
        $I->click('Accounts');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/h1/a[1]');
        $I->fillField(' Account Name ', 'Nurul Hassan');
        $I->fillField( 'Account Number', '035163171' );
        $I->selectOption( 'Account Currency', 'Euro');
        $I->fillField( 'Opening Balance', '100000000');
        $I->fillField( 'Bank Name', 'State Bank Of India');
        $I->fillField( 'Bank Phone','97878786');
        $I->fillField('Bank Address', 'Haryana Gandhi Road');
        $I->click( 'Submit' );
        $I->wait(2);
        $I->see( 'Nurul Hassan' );
        
        /**
         * assert if the Account is edited
         */
        $I->click('Nurul Hassan');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/div[2]/div[2]/div/div[1]/a');
        $I->see('Update Account');
        

        /**
         * assert if Account Name field is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-banking&tab=accounts');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/h1/a[1]');
        $I->fillField( 'Account Number', '035163171' );
        $I->selectOption( 'Account Currency', 'Euro');
        $I->fillField( 'Opening Balance', '100000000');
        $I->fillField( 'Bank Name', 'State Bank Of India');
        $I->fillField( 'Bank Phone','97878786');
        $I->fillField('Bank Address', 'Haryana Gandhi Road');
        $I->click( 'Submit' );
        $I->SeeFieldEmpty( $I->grabTextFrom('//*[@id="name"]') );
        $I->wait(2);

        /**
         * assert if the Account Number is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-banking&tab=accounts');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/h1/a[1]');
        $I->fillField( 'Account Name', 'Grunt' );
        $I->selectOption( 'Account Currency', 'Euro');
        $I->fillField( 'Opening Balance', '100000000');
        $I->fillField( 'Bank Name', 'State Bank Of India');
        $I->fillField( 'Bank Phone','97878786');
        $I->fillField('Bank Address', 'Haryana Gandhi Road');
        $I->click( 'Submit' );
        $I->SeeFieldEmpty( $I->grabTextFrom('//*[@id="number"]') );
    }
}
