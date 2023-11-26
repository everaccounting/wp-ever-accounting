/**
 * External dependencies
 */
import { SectionHeader, Form } from '@eac/components';
import { AddCustomer, AddItem } from '@eac/editor';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
function Help() {
	return (
		<>
			<SectionHeader title={__('Help', 'wp-ever-accounting')} />
			<AddCustomer />
		</>
	);
}

export default Help;
