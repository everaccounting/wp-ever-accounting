/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Create() {
	return (
		<>
			<SectionHeader title={__('Create', 'wp-ever-accounting')} />
		</>
	);
}

export default Create;
