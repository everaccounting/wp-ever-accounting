<?php
defined('ABSPATH') || exit();

function eaccounting_insert_invoice($args){
	$taxes = [];

	$tax_total = 0;
	$sub_total = 0;
	$discount_total = 0;

	$discount = $args['discount'];
}


function eaccounting_insert_invoice_item($args){
	$taxes = [];

	$tax_total = 0;
	$sub_total = 0;
	$discount_total = 0;

	$discount = $args['discount'];
}
