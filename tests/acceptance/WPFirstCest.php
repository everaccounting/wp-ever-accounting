<?php 

class WPFirstCest
{
    public function _before(AcceptanceTester $I)
    {

    }

    // tests
    public function PluginActivateCheck(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->amGoingTo('?page=eaccounting');
        $I->amGoingTo('?page=ea-items');
        $I->click([ 'page-title-action' => 'Add New' ]);
    }    
}

