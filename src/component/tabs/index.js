import React from 'react';
import { translate as __ } from 'lib/locale';
import PropTypes from 'prop-types';

const isCurrent = ( page, item ) => page === item.path || page === 'redirect' && item.path === '';
const tabs = () => [
	{
		name: __( 'Redirects' ),
		value: '',
	},
	{
		name: __( 'Groups' ),
		value: 'groups',
	},
	{
		name: __( 'Site' ),
		value: 'site',
	},
	{
		name: __( 'Log' ),
		value: 'log',
	},
	{
		name: __( '404s' ),
		value: '404s',
	},
	{
		name: __( 'Import/Export' ),
		value: 'io',
	},
	{
		name: __( 'Options' ),
		value: 'options',
	},
	{
		name: __( 'Support' ),
		value: 'support',
	},
];

const Tabs = props => {
	const { onChangePage, } = props;
	const menu = tabs().filter(option=> option.path !== '');
	if ( menu.length < 2 ) {
		return null;
	}

	return(
		<nav className="nav-tab-wrapper eaccounting-nav-tab-wrapper">
			{
				menu.map( ( item, pos ) => <MenuItem key={ pos } item={ item } isCurrent={ isCurrent( page, item ) } onClick={ onChangePage } /> )
			}
		</nav>
	)

};
Tabs.propTypes = {
	onChangePage: PropTypes.func.isRequired,
};

export default Tabs;
