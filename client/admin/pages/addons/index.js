/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Addons() {
	return (
		<>
			<SectionHeader title={__('Addons', 'wp-ever-accounting')} />
		</>
	);
}

export default Addons;
