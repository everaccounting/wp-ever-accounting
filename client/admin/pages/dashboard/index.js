/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Dashboard( props ) {
	console.log( props );
	return (
		<>
			<SectionHeader title={ <>{ __( 'Dashboard', 'wp-ever-accounting' ) }</> } actions="test" menu="menu" isCard={ true }/>
		</>
	);
}

export default Dashboard;
