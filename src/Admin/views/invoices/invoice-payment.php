<?php
/**
 * Invoice Payment form.
 *
 * @since 1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Invoices
 *
 * @var \EverAccounting\Models\Invoice $document Invoice object.
 */
defined( 'ABSPATH' ) || exit();
$payment = new \EverAccounting\Models\Payment();
$payment->set_document_id( $document->get_id() );
$payment->set_amount( $document->get_total() );
$payment->set_date( current_time( 'mysql' ) );
$payment->set_customer_id( $document->get_contact_id() );
include dirname( __DIR__ ) . '/payments/payment-form.php';
