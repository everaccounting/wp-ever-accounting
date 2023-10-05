<?php
/**
 * Bill Payment form.
 *
 * @since 1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Bills
 *
 * @var \EverAccounting\Models\Bill $document Bill object.
 */
defined( 'ABSPATH' ) || exit();
$expense = new \EverAccounting\Models\Expense();
$expense->set_document_id( $document->get_id() );
$expense->set_amount( $document->get_total() );
$expense->set_date( current_time( 'mysql' ) );
$expense->set_vendor_id( $document->get_contact_id() );
include dirname( __DIR__ ) . '/expenses/expense-form.php';
