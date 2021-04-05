<?php 

class PaymentsCest
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
    public function Payments(AcceptanceTester $I)
    {
        /**
         * create a payment
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-expenses&tab=bills');
        $I->click('//*[@id="wpbody-content"]/div[2]/nav/a[2]');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->click('//*[@id="payment_date"]');
        $I->click(20);
        $I->selectOption('Account', 'Cash');
        $I->fillField('Amount', '5000');
        $I->click('//*[@id="ea-payment-form"]/div/div[2]/div/div[4]/span/span[1]/span');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Mike Drint');
        $I->selectOption('Currency', 'Yen');
        $I->fillField('Company', 'Skylark Solutions');
        $I->fillField('Email', 'skylark_solutions@gmail.com');
        $I->fillField('Phone', '9904653524');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-payment-form"]/div/div[2]/div/div[4]/span/span[1]/span');
        $I->click('//*[@id="select2-vendor_id-results"]');
        $I->selectOption('Category', 'Other');
        $I->fillField('Description', 'I am tetsting payments section');
        $I->fillField('Reference', '090909');
        $I->click('//*[@id="submit"]');
        $I->wait(2);
        $I->see('Mike Drint');
        $I->wait(3);

        /**
         * assert if Date is empty
         */
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->selectOption('Account', 'Cash');
        $I->fillField('Amount', '5000');
        $I->click('//*[@id="ea-payment-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-vendor_id-results"]');
        $I->selectOption('Category','Other');
        $I->selectOption('Payment Method', 'Cash');
        $I->click('//*[@id="submit"]');
        $I->cantSeeInField('Date','2021-02-07');
        $I->wait(3);

        /**
         * assert if Account is empty 
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-expenses&tab=payments');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        
        $I->fillField('Amount', '5000');
        $I->click('//*[@id="ea-payment-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-vendor_id-results"]');
        $I->click('//*[@id="payment_date"]');
        $I->click(20);
        $I->selectOption('Category','Other');
        $I->selectOption('Payment Method', 'Cash');
        $I->click('//*[@id="submit"]');
        $I->cantSeeInField('Account','Cash');
        $I->wait(3);

        /**
         * assert if Amount is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-expenses&tab=payments');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');

        $I->selectOption('Account', 'Cash');
        $I->click('//*[@id="ea-payment-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-vendor_id-results"]');
        $I->click('//*[@id="payment_date"]');
        $I->click(20);
        $I->selectOption('Category','Other');
        $I->selectOption('Payment Method', 'Cash');
        $I->click('//*[@id="submit"]');
        $I->wait(3);

        /**
         * assert if Category is not selected
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-expenses&tab=payments');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');

        $I->click('//*[@id="payment_date"]');
        $I->click(20);
        $I->selectOption('Account', 'Cash');
        $I->click('//*[@id="ea-payment-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-vendor_id-results"]');
        $I->selectOption('Payment Method', 'Cash');
        $I->fillField('Amount', '5000');
        $I->click('//*[@id="submit"]');
        $I->cantSeeInField('Category','Other');
        
        /**
         * assert if Payment Method is not bank transfer
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-expenses&tab=payments');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');

        $I->click('//*[@id="payment_date"]');
        $I->click(20);
        $I->selectOption('Account', 'Cash');
        $I->click('//*[@id="ea-payment-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-vendor_id-results"]');
        $I->selectOption('Category','Other');
        $I->fillField('Amount', '5000');
        $I->selectOption('Payment Method', 'Select payment method');
        $I->click('//*[@id="submit"]');
        $I->wait(2);
        $I->cantSeeInField('Payment Method', 'Cash');
    }
}
