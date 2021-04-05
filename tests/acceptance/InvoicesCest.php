<?php 

class InvoicesCest
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
    public function Invoices(AcceptanceTester $I)
    {
        /**
         * assert if invoice is created and status set to paid
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-sales');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->click('//*[@id="select2-customer_id-1-container"]');
        $I->click( '/html/body/span/span/span[2]/a' );
        $I->wait( 1 );
        $I->fillField('Name',  'John Wick');
        $I->selectOption('Currency', 'Euro');
        $I->fillField('Company', 'weLab');
        $I->fillField('Email', 'john@gmail.com');
        $I->fillField('Phone', '01906759899');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-invoice-form"]/div/div[2]/div[1]/div[1]/span/span[1]/span');
        $I->wait(2);
        $I->click('//*[@id="select2-customer_id-1-results"]');
        $I->selectOption('Currency', 'Yen');
        $I->click('//*[@id="issue_date"]');
        $I->click('//*[@id="ui-datepicker-div"]/div/a[1]/span');
        $I->click('10');
        $I->click('//*[@id="due_date"]');
        $I->click('15');
        $I->fillField('Invoice Number ','INV-005');
        $I->fillField('Order Number', '21');
        $I->selectOption('Category','Sales');

        /**
         * adding line item
         */
        $I->click('Add Line Item');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Play Station 5');
        $I->fillField('Sale price', '80808080');
        $I->fillField('Purchase price', '70707070');
        $I->wait(2);
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span');
        $I->wait(2);
        $I->click('/html/body/span/span');
        $I->click('//*[@id="submit"]');
        $I->wait(3);
        $I->click('//*[@id="post-body-content"]/div/div[1]/div[2]/button');
        $I->wait(2);
        $I->click('//*[@id="date"]');
        $I->wait(1);
        $I->click(4);
        $I->selectOption('Account','Cash');
        $I->wait(2);
        $I->click('/html/body/div[4]/div[1]/div/div[2]/button[2]');
        $I->see('Paid');

        /**
         * assert if Customer Name is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-sales');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->selectOption('Currency', 'Euro');
        $I->wait(2);
        $I->selectOption('Currency', 'Yen');
        $I->click('//*[@id="issue_date"]');
        $I->click('//*[@id="ui-datepicker-div"]/div/a[1]/span');
        $I->click('10');
        $I->click('//*[@id="due_date"]');
        $I->click('15');
        $I->fillField('Invoice Number ','INV-005');
        $I->fillField('Order Number', '21');
        $I->selectOption('Category','Sales');

        /**
         * adding line item
         */
        $I->click('Add Line Item');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Play Station 6');
        $I->fillField('Sale price', '80808080');
        $I->fillField('Purchase price', '70707070');
        $I->wait(2);
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span');
        $I->wait(2);
        $I->click('/html/body/span/span');
        $I->click('//*[@id="submit"]');
        $I->see('Select Customer');
        $I->wait(2);

        /**
         * assert if currency is empty by-default selected to US dollar
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-sales');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a');
        $I->click('//*[@id="select2-customer_id-1-container"]');
        $I->click( '/html/body/span/span/span[2]/a' );
        $I->wait( 1 );
        $I->fillField('Name',  'John Wick 2');
        $I->fillField('Company', 'weLab');
        $I->fillField('Email', 'john@gmail.com');
        $I->fillField('Phone', '01906759899');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-invoice-form"]/div/div[2]/div[1]/div[1]/span/span[1]/span');
        $I->wait(2);
        $I->click('//*[@id="select2-customer_id-1-results"]');
        $I->wait(2);
        $I->click('//*[@id="issue_date"]');
        $I->click('//*[@id="ui-datepicker-div"]/div/a[1]/span');
        $I->click('10');
        $I->click('//*[@id="due_date"]');
        $I->click('15');
        $I->fillField('Invoice Number ','INV-005');
        $I->fillField('Order Number', '21');
        $I->selectOption('Category','Sales');

        /**
         * adding line item
         */
        $I->click('Add Line Item');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Play Station 7');
        $I->fillField('Sale price', '80808080');
        $I->fillField('Purchase price', '70707070');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-document__line-items"]/tr/td[2]/span/span[1]/span');
        $I->click('/html/body/span/span');
        $I->see('US Dollar');
        
    }
}
