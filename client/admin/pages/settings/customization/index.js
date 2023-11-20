/**
 * External dependencies
 */
import { Input, Button, Panel, Text, Space, Card } from '@eac/components';
import { navigate, getQuery } from '@eac/navigation';
/**
 * WordPress dependencies
 */
import { TabPanel } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import Invoice from "~/pages/settings/customization/invoice/invoice.js";

function Customization() {
	const query = getQuery();
	const tabs = [
		{
			key: 'invoice',
			label: __( 'Invoice' ),
			content: <Invoice />
		},
		{
			key: 'estimate',
			label: __( 'Estimate' ),
			content: <div>Estimates</div>,
		},
		{
			key: 'payment',
			label: __( 'Payment' ),
			content: <div>Payment</div>,
		},
		{
			key: 'credit',
			label: __( 'Credit' ),
			content: <div>Credit</div>,
		},
		{
			key: 'expense',
			label: __( 'Expense' ),
			content: <div>Expense</div>,
		},
	];
	return (
		<Card
			title={ <Text size="16">{ __( 'Company Details', 'wp-ever-accounting' ) }</Text> }
			actions={
				<Button onClick={ () => navigate( 'settings' ) } isPrimary>
					{ __( 'Back', 'wp-ever-accounting' ) }
				</Button>
			}
			tabs={ tabs }
			activeTab={ query?.tab || 'invoice' }
			onTabChange={ ( tab ) => navigate( { tab } ) }
		>

		</Card>
	);
}

export default Customization;
