<?php
/**
 * Admin View: Page - Reports
 *
 * @var array  $tabs
 * @var string $current_tab
 */
defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__ ) .'/reports/class-ea-admin-report.php';
$report = new EAccounting_Admin_Report();
$report->output_report();
