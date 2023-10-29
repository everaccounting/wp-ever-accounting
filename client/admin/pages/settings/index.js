/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Settings() {
	return (
		<>
			<SectionHeader title={ __( 'Settings', 'wp-ever-accounting' ) } />
		</>
	);
}

export default Settings;
