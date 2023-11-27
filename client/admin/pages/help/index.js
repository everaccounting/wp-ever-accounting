/**
 * External dependencies
 */
import { SectionHeader, Form } from '@eac/components';
import { AddCustomer, AddItem, AddPayment } from '@eac/editor';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
function Help() {
	return (
		<>
			<SectionHeader title={__('Help', 'wp-ever-accounting')} />

			<AddPayment />
		</>
	);
}

export default Help;
