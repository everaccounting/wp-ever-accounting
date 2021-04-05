<?php 

use _generated\AcceptanceTesterActions;

class TransfersCest
{
    public function _before(AcceptanceTester $I)
    {
        /**
         * login as admin
         */
        $I->wantTo( 'Check All Possible Outcomes Of Transfers' );
        $I->loginAsAdmin();
        $I->amOnPluginsPage();
        $I->activatePlugin( 'wp-ever-accounting' );
    }

    

    // tests
    public function Transfers(AcceptanceTester $I) {

        /**
         * assert if the Transfer is created
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-banking');
        $I->click('Transfers');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->selectOption('From Account', 'Cash');
        $I->click('//*[@id="select2-to_account_id-2-container"]');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Account Name','Tracy Williams');
        $I->fillField('Account Number', '87878787');
        $I->selectOption('Account Currency', 'US Dollar');
        $I->fillField( 'Opening Balance', '9090909');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]'); //submit modal values
        $I->click('//*[@id="ea-transfer-form"]/div/div[2]/div/div[2]/span/span[1]/span');
        $I->click('//*[@id="select2-to_account_id-2-results"]');
        $I->wait(5);
        $I->fillField('Amount', '1000');
        $I->click('//*[@id="date"]');
        $I->click('23');
        $I->selectOption('Payment Method', 'Bank Transfer');
        $I->fillField( 'Reference', 'Ref-01');
        $I->fillField('Description', 'I am all ears');
        $I->click( 'Submit' );
        $I->wait(3);
        $I->see('Tracy Williams');

        /**
         * assert if source account and destination account are same
         */
        $I->click('Transfers');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->selectOption('From Account', 'Cash');
        $I->selectOption('To Account', 'Cash');
        $I->fillField('Amount', '1000');
        $I->click('//*[@id="date"]');
        $I->click('23');
        $I->selectOption('Payment Method', 'Bank Transfer');
        $I->fillField( 'Reference', 'Ref-01');
        $I->fillField('Description', 'I am all ears');
        $I->click( 'Submit' );
        $I->wait(1);
        $I->see("Source and Destination account can't be same.");
        

        /**
         * assert if From Account is empty
         */
        $I->click('Transfers');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->wait(2);
        $I->selectOption('To Account', 'Cash');
        $I->fillField('Amount', '1000');
        $I->click('//*[@id="date"]');
        $I->click('23');
        $I->selectOption('Payment Method', 'Bank Transfer');
        $I->fillField( 'Reference', 'Ref-01');
        $I->fillField('Description', 'I am all ears');
        $I->click( 'Submit' );
        $I->wait(2);
        $I->see('Select Account');

        /**
         * assert if To Account is empty 
         */
        $I->click('Transfers');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->wait(2);
        $I->selectOption('From Account', 'Cash');
        $I->fillField('Amount', '1000');
        $I->click('//*[@id="date"]');
        $I->click('23');
        $I->selectOption('Payment Method', 'Bank Transfer');
        $I->fillField( 'Reference', 'Ref-01');
        $I->fillField('Description', 'I am all ears');
        $I->click( 'Submit' );
        $I->wait(2);
        $I->see('Select Account');

        /**
         * assert of Amount is empty
         */
        $I->click('Transfers');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->wait(2);
        $I->selectOption('From Account', 'Cash');
        $I->selectOption('To Account', 'Cash');
        $I->click('//*[@id="date"]');
        $I->click('23');
        $I->selectOption('Payment Method', 'Bank Transfer');
        $I->fillField( 'Reference', 'Ref-01');
        $I->fillField('Description', 'I am all ears');
        $I->click( 'Submit' );
        $I->wait(2);
        $I->SeeFieldEmpty('//*[@id="amount"]');

        /**
         * assert if Date is empty
         */
        $I->click('Transfers');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->wait(2);
        $I->selectOption('From Account', 'Cash');
        $I->selectOption('To Account', 'Cash');
        $I->fillField('Amount', '1000');
        $I->selectOption('Payment Method', 'Bank Transfer');
        $I->fillField( 'Reference', 'Ref-01');
        $I->fillField('Description', 'I am all ears');
        $I->click( 'Submit' );
        $I->wait(2);
        $I->SeeFieldEmpty('//*[@id="date"]');

        /**
         * assert if Payment Method is Cash by default provided by the system
         */
        $I->click('Transfers');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->wait(2);
        $I->selectOption('From Account', 'Cash');
        $I->selectOption('To Account', 'Cash');
        $I->fillField('Amount', '1000');
        $I->click('//*[@id="date"]');
        $I->click('23');
        $I->fillField( 'Reference', 'Ref-01');
        $I->fillField('Description', 'I am all ears');
        $I->click( 'Submit' );
        $I->wait(2);
        $I->see('Cash');

    }
}
