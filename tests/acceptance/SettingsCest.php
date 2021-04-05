<?php 

class SettingsCest
{
    public function _before(AcceptanceTester $I)
    {
        /**
         * login as admin
         */
        $I->wantTo( 'Check All Possible Outcomes Of Settings' );
        $I->loginAsAdmin();
        $I->amOnPluginsPage();
        $I->activatePlugin( 'wp-ever-accounting' );
    }

    // tests
    public function Settings(AcceptanceTester $I) {

        /**
         * filling all the settings field options
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-settings&tab=general');  
        $I->fillField('Name','Byetever LLC');  
        $I->fillField('Email', 'byteever@gmail.com');
        $I->fillField('Phone Number', '01906759899');
        $I->fillField('VAT Number', '1990');
        $I->fillField('Street', 'Avenue-8, Road-11, Mirpur DOHS');
        $I->fillField('City', 'Dhaka');
        $I->fillField('State', 'Dhaka Sadar');
        $I->fillField('Postcode', '9001');
        $I->selectOption('Country', 'Belgium');
        $I->fillField('Logo', '/home/cupid/Desktop/Photos/person 1.jpeg');
        $I->click('//*[@id="eaccounting_settings[financial_year_start]"]');
        $I->click(4);
        $I->selectOption('Account','Cash');
        $I->selectOption('Currency', 'Euro');
        $I->selectOption('Payment Method', 'Bank Transfer');
        $I->click('Save Changes');
        $I->see('Settings updated.');

        /**
         * assert if Company Field is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-settings&tab=general');  
        $I->fillField('Name','');  
        $I->fillField('Email', 'byteever@gmail.com');
        $I->fillField('Phone Number', '01906759899');
        $I->fillField('VAT Number', '1990');
        $I->fillField('Street', 'Avenue-8, Road-11, Mirpur DOHS');
        $I->fillField('City', 'Dhaka');
        $I->fillField('State', 'Dhaka Sadar');
        $I->fillField('Postcode', '9001');
        $I->selectOption('Country', 'Belgium');
        $I->fillField('Logo', '/home/cupid/Desktop/Photos/person 1.jpeg');
        $I->click('//*[@id="eaccounting_settings[financial_year_start]"]');
        $I->click(4);
        $I->selectOption('Account','Cash');
        $I->selectOption('Currency', 'Euro');
        $I->selectOption('Payment Method', 'Bank Transfer');
        $I->click('Save Changes');
        
        /**
         * General Invoices(tab)
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-settings&tab=general&section=invoices');
        $I->fillField('Invoice Prefix', 'INV-2-');
        $I->fillField('Minimum Digits', '6');
        $I->fillField('Invoice Terms', 'This a test term');
        $I->fillField('Invoice Note', 'Hello XYZ');
        $I->selectOption('Invoice Due', 'Due within 15 days');
        $I->fillField('Item Label', 'Item-2');
        $I->fillField('Price Label', 'Price-2');
        $I->fillField('Quantity Label', 'Quantity');
        $I->click('Save Changes');
        $I->see('Settings updated.');

        /**
         * General Bills(tab) 
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-settings&tab=general&section=bills');
        $I->fillField('Bill Prefix', 'BILL-2-');
        $I->fillField('Bill Digits', '6');
        $I->fillField('Bill Terms & Conditions', 'This a test term');
        $I->fillField('Bill Note', 'Hello XYZ');
        $I->selectOption('Bill Due', 'Due within 15 days');
        $I->fillField('Item Label', 'Item-2');
        $I->fillField('Price Label', 'Price-2');
        $I->fillField('Quantity Label', 'Quantity');
        $I->click('Save Changes');
        $I->see('Settings updated.');

        /**
         * General (Taxes)
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-settings&tab=general&section=taxes');
        $I->click('//*[@id="eaccounting_settings[tax_subtotal_rounding]"]');
        $I->selectOption('Prices entered with tax', 'No, I will enter prices exclusive of tax');
        $I->selectOption('Display tax totals','As individual tax rates');
        $I->click('Save Changes');
        $I->see('Settings updated.');
        $I->wait(3);


    }
}    