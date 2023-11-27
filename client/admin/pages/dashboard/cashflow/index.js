/**
 * WordPress dependencies
 */
import { Card, CardBody, CardHeader } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

function CashFlow() {
	return (
		<Card size="small" style={{ marginTop: '20px' }}>
			<CardHeader size="xsmall">{__('Cash Flow', 'wp-ever-accounting')}</CardHeader>
			<CardBody>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius, possimus.</CardBody>
		</Card>
	);
}

export default CashFlow;
