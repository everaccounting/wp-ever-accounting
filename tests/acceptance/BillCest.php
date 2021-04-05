<?php 

class BillCest
{
    public function _before(AcceptanceTester $I)
    {
        /**
         * login as admin
         */
        $I->wantTo( 'Check All Possible Outcomes Of Invoices' );
        $I->loginAsAdmin();
        $I->amOnPluginsPage();
        $I->activatePlugin( 'wp-ever-accounting' );
    }

    // tests
    public function Bill(AcceptanceTester $I)
    {   
        /**
         * assert if bill is created and status set to paid
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-expenses&tab=bills');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->click('//*[@id="ea-bill-form"]/div/div[2]/div[1]/div[1]/span/span[1]/span/span[2]');
        $I->click( '/html/body/span/span/span[2]/a' );
        $I->wait( 1 );
        $I->fillField('Name',  'Billie Joe Armstrong');
        $I->selectOption('Currency', 'Euro');
        $I->fillField('Company', 'Plugin-Ever');
        $I->fillField('Email', 'plugin_ever@gmail.com');
        $I->fillField('Phone', '01906759888');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-bill-form"]/div/div[2]/div[1]/div[1]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-vendor_id-1-results"]');
        $I->selectOption('Currency', 'Bangladeshi Taka');
        $I->click('//*[@id="issue_date"]');
        $I->click('10');
        $I->click('//*[@id="due_date"]');
        $I->click('15');
        $I->fillField('Bill Number  ','BILL-005');
        $I->fillField('Order Number', '30');
        $I->selectOption('Category','Other');
        

         /**
         * adding line item
         */
        $I->click('Add Line Item');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Red Dead Redemption 2');
        $I->fillField('Sale price', '8080');
        $I->fillField('Purchase price', '7070');
        $I->wait(2);
        $I->click('body > div.ea-modal > div.ea-modal__content > div > div.ea-modal__footer > button.button.button-primary');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span/span[2]');
        $I->wait(2);
        $I->click('/html/body/span/span');
        $I->click('//*[@id="submit"]');
        $I->click('//*[@id="post-body-content"]/div/div[1]/div[2]/button');
        $I->wait(2);
        $I->click('//*[@id="date"]');
        $I->wait(1);
        $I->click(4);
        $I->selectOption('Account','Cash');
        $I->wait(2);
        $I->click('/html/body/div[4]/div[1]/div/div[2]/button[2]');

        /**
         * assert if bill is set to paid and created
         */
        $I->see('Paid');
        

        /**
         * assert if Customer Name is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-expenses&tab=bills');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->selectOption('Currency', 'Euro');
        $I->wait(2);
        $I->selectOption('Currency', 'Yen');
        $I->click('//*[@id="issue_date"]');
        $I->click('//*[@id="ui-datepicker-div"]/div/a[1]/span');
        $I->click('10');
        $I->click('//*[@id="due_date"]');
        $I->click('15');
        $I->fillField('Bill Number ','INV-005');
        $I->fillField('Order Number', '21');
        $I->selectOption('Category','Other');

        $I->click('Add Line Item');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Red Dead Redemption 2');
        $I->fillField('Sale price', '8080');
        $I->fillField('Purchase price', '7070');
        $I->wait(2);
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span');
        $I->wait(2);
        $I->click('/html/body/span/span');
        $I->click('//*[@id="submit"]');
        $I->see('Select Vendor');
        $I->wait(2);


        /**
         * assert if currency is empty by-default selected to Pound Sterling
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-expenses&tab=bills');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->click('//*[@id="ea-bill-form"]/div/div[2]/div[1]/div[1]/span/span[1]/span/span[2]');
        $I->click( '/html/body/span/span/span[2]/a' );
        $I->wait( 1 );
        $I->fillField('Name',  'Billie Joe Armstrong 3');
        $I->selectOption('Currency', 'Pound Sterling');
        $I->fillField('Company', 'Plugin-Ever');
        $I->fillField('Email', 'plugin_ever@gmail.com');
        $I->fillField('Phone', '01906759877');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-bill-form"]/div/div[2]/div[1]/div[1]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-vendor_id-1-results"]');
        $I->click('//*[@id="issue_date"]');
        $I->click('10');
        $I->click('//*[@id="due_date"]');
        $I->click('20');
        $I->fillField('Bill Number  ','BILL-005');
        $I->fillField('Order Number', '300');
        $I->selectOption('Category','Other');
        
        $I->click('Add Line Item');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Red Dead Redemption 2');
        $I->fillField('Sale price', '8080');
        $I->fillField('Purchase price', '7070');
        $I->wait(2);
        $I->click('body > div.ea-modal > div.ea-modal__content > div > div.ea-modal__footer > button.button.button-primary');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span/span[2]');
        $I->wait(2);
        $I->click('/html/body/span/span');
        $I->see('Pound Sterling');

    }
}
