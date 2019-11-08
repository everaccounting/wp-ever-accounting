<?php
function eaccounting_edit_account($data){
//	$account_id = eaccounting_insert_account(array(
//		'id'              => absint($data['id'])
//			'name'            =>
//'number'          =>
//'opening_balance' =>
//'bank_name'       =>
//'bank_phone'      =>
//'bank_address'    =>
//'status'          =>
//	))
//	eaccounting()->notices->add('test');
}
add_action('eaccounting_admin_post_edit_account', 'eaccounting_edit_account');
