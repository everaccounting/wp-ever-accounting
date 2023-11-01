/**
 * External dependencies
 */
import { SectionHeader, Dropdown, Placeholder } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Reports() {
	return (
		<>
			<SectionHeader title={ __( 'Reports', 'wp-ever-accounting' ) } />
			<Placeholder.Avatar size="large" active />
			<Placeholder.Avatar size="small" active />
			<Placeholder.Button active />
			<Placeholder.Button block active />
			<Placeholder.Image block active />
			<Placeholder.Input block active />
			<Placeholder.Input active />
			<Placeholder.Node active style={{display:'block'}}/>
		</>
	);
}

export default Reports;
