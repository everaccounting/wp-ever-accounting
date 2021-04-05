<?php 

class RevenuesCest
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
    public function Revenues(AcceptanceTester $I)
    {   
        /**
         * create a revenue
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-sales');
        $I->click('//*[@id="wpbody-content"]/div[2]/nav/a[2]');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->click('//*[@id="payment_date"]');
        $I->click(20);
        $I->selectOption('Account', 'Cash');
        $I->fillField('Amount', '5000');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Harry Potter');
        $I->selectOption('Currency', 'US Dollar');
        $I->fillField('Company', 'Pluginever');
        $I->fillField('Email', 'pluginever@gmail.com');
        $I->fillField('Phone', '9904653524');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-customer_id-results"]');
        $I->selectOption('Category', 'Deposit');
        $I->fillField('Description', 'I am tetsting revenues section');
        $I->fillField('Reference', '090909');
        $I->click('//*[@id="submit"]');
        $I->wait(2);
        $I->see('Harry Potter');


        /**
         * assert if Date is empty
         */
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->selectOption('Account', 'Cash');
        $I->fillField('Amount', '5000');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Harry Potter');
        $I->selectOption('Currency', 'US Dollar');
        $I->fillField('Company', 'Pluginever');
        $I->fillField('Email', 'pluginever@gmail.com');
        $I->fillField('Phone', '9904653524');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-customer_id-results"]');
        $I->selectOption('Category', 'Deposit');
        $I->fillField('Description', 'I am tetsting revenues section');
        $I->fillField('Reference', '090909');
        $I->click('//*[@id="submit"]');
        $I->cantSeeInField('Date','22/07/2020');


        /**
         * assert if Account is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-sales');
        $I->click('//*[@id="wpbody-content"]/div[2]/nav/a[2]');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->click('//*[@id="payment_date"]');
        $I->click(20);
        $I->fillField('Amount', '5000');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Harry Potter');
        $I->selectOption('Currency', 'US Dollar');
        $I->fillField('Company', 'Pluginever');
        $I->fillField('Email', 'pluginever@gmail.com');
        $I->fillField('Phone', '9904653524');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-customer_id-results"]');
        $I->selectOption('Category', 'Deposit');
        $I->fillField('Description', 'I am tetsting revenues section');
        $I->fillField('Reference', '090909');
        $I->click('//*[@id="submit"]');
        $I->cantSeeInField('Account', 'Cash');
        

        /**
         * assert if Amount is empty
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-sales');
        $I->click('//*[@id="wpbody-content"]/div[2]/nav/a[2]');
        $I->click('//*[@id="wpbody-content"]/div[2]/nav/a[2]');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->click('//*[@id="payment_date"]');
        $I->click(20);
        $I->selectOption('Account', 'Cash');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Harry Potter');
        $I->selectOption('Currency', 'US Dollar');
        $I->fillField('Company', 'Pluginever');
        $I->fillField('Email', 'pluginever@gmail.com');
        $I->fillField('Phone', '9904653524');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-customer_id-results"]');
        $I->selectOption('Category', 'Deposit');
        $I->fillField('Description', 'I am tetsting revenues section');
        $I->fillField('Reference', '090909');
        $I->click('//*[@id="submit"]');
        $I->wait(3);
       

        /**
         * assert if Category is not selected
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-sales');
        $I->click('//*[@id="wpbody-content"]/div[2]/nav/a[2]');
        $I->click('//*[@id="wpbody-content"]/div[2]/nav/a[2]');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->click('//*[@id="payment_date"]');
        $I->click(20);
        $I->selectOption('Account', 'Cash');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Harry Potter 10');
        $I->selectOption('Currency', 'US Dollar');
        $I->fillField('Company', 'Pluginever');
        $I->fillField('Email', 'pluginever@gmail.com');
        $I->fillField('Phone', '9904653524');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-customer_id-results"]');
        $I->fillField('Description', 'I am tetsting revenues section');
        $I->fillField('Reference', '090909');
        $I->click('//*[@id="submit"]');
        $I->cantSeeInField('Category', 'Deposit');
        $I->wait(5);

        /**
         * assert if Payment Method is not bank transfer
         */
        $I->amOnPage('wp-admin/admin.php?page=ea-sales');
        $I->click('//*[@id="wpbody-content"]/div[2]/nav/a[2]');
        $I->click('//*[@id="wpbody-content"]/div[2]/nav/a[2]');
        $I->click('//*[@id="wpbody-content"]/div[2]/div/a[1]');
        $I->click('//*[@id="payment_date"]');
        $I->click(20);
        $I->selectOption('Account', 'Cash');
        $I->fillField('Amount', '90909090');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('/html/body/span/span/span[2]/a');
        $I->fillField('Name', 'Harry Potter 10');
        $I->selectOption('Currency', 'US Dollar');
        $I->fillField('Company', 'Pluginever');
        $I->fillField('Email', 'pluginever@gmail.com');
        $I->fillField('Phone', '9904653524');
        $I->click('/html/body/div[5]/div[1]/div/div[2]/button[2]');
        $I->click('//*[@id="ea-revenue-form"]/div/div[2]/div/div[4]/span/span[1]/span/span[2]');
        $I->click('//*[@id="select2-customer_id-results"]');
        $I->selectOption('Category', 'Deposit');
        $I->wait(1);
        $I->selectOption('Payment Method', 'Select payment method');
        $I->click('//*[@id="submit"]');
        $I->wait(2);
        $I->cantSeeInField('//*[@id="ea-revenue-form"]/div/div[2]/div/div[6]/span/span[1]/span', 'Bank Transfer');
        $I->wait(10);
    }
}
