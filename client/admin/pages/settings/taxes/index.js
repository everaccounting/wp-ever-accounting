/**
 * External dependencies
 */
import { Form, Button, Card, Text, Space } from '@eac/components';
import { navigate, getQuery } from '@eac/navigation';
/**
 * WordPress dependencies
 */
import { lazy } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const Options = lazy( () => import( './options' ) );
const Rates = lazy( () => import( './rates' ) );

function Taxes() {
	const tabs = [
		{
			key: 'options',
			label: __( 'Options', 'wp-ever-accounting' ),
			content: <Options />,
		},
		{
			key: 'rates',
			label: __( 'Rates', 'wp-ever-accounting' ),
			content: <Rates />,
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

export default Taxes;
