/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Tools() {
	return (
		<>
			<SectionHeader card title={ __( 'Tools', 'wp-ever-accounting' ) } />
		</>
	);
}

export default Tools;
