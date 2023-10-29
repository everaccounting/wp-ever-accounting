/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Edit() {
	return (
		<>
			<SectionHeader title={__('Edit', 'wp-ever-accounting')} />
		</>
	);
}

export default Edit;
