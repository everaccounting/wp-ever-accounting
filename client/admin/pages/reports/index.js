/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Reports() {
	return (
		<>
			<SectionHeader title={__('Reports', 'wp-ever-accounting')} />
		</>
	);
}

export default Reports;
