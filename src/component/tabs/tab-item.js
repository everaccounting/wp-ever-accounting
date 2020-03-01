/**
 * External dependencies
 */

import React from 'react';
import PropTypes from 'prop-types';


const TabItem = props => {
	const { item, isCurrent, onClick } = props;
	const clicker = ev => {
		ev.preventDefault();
		onClick( item.value, url );
	};

	return (
		<li>
			<a className={ isCurrent ? 'current' : '' } href="/"  onClick={ clicker }>
				{ item.name }
			</a>
		</li>
	);
};

TabItem.propTypes = {
	item: PropTypes.object.isRequired,
	isCurrent: PropTypes.bool.isRequired,
	onClick: PropTypes.func.isRequired,
};

export default TabItem;
