/**
 * External dependencies
 */
import { Card } from '@eac/components';
import { navigate, getQuery } from '@eac/navigation';
/**
 * WordPress dependencies
 */
import { lazy } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const Company = lazy( () => import( './company' ) );

function General() {
	const tabs = [
		{
			key: 'company',
			label: __( 'Company', 'wp-ever-accounting' ),
			content: <Company />,
		},
	];

	return (
		<Card
			tabs={ tabs }
			activeTab={ getQuery()?.tab || tabs[ 0 ].key }
			onTabChange={ ( tab ) => navigate( { tab } ) }
		/>
	);
}

export default General;
