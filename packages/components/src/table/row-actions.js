/**
 * External dependencies
 */

import React from 'react';
import PropTypes from 'prop-types';
import {DropdownMenu} from '@wordpress/components';

const RowActions = props => {
	return (
		<div className="item-actions">
			<DropdownMenu
				icon="ellipsis"
				position="bottom right"
				controls={props.controls}
			/>
		</div>
	);
};

RowActions.propTypes = {
	controls: PropTypes.array,
};


export default RowActions;
