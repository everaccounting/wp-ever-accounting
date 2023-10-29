/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function View() {
	return (
		<>
			<SectionHeader title={__('View', 'wp-ever-accounting')} />
		</>
	);
}

export default View;
