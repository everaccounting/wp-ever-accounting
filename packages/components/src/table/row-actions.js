/**
 * External dependencies
 */

import React from 'react';
import PropTypes from 'prop-types';
import {DropdownMenu} from '@wordpress/components';

const RowActions = props => {
	const {actions, disabled} = props;
	const controls = actions.map((action) => {
		action.disabled = disabled;
		return action;
	});


	return (
		<div className="item-actions">
			<DropdownMenu
				icon="ellipsis"
				position="bottom right"
				controls={controls}
			/>
		</div>
	);
};

RowActions.propTypes = {
	actions: PropTypes.array,
	disabled: PropTypes.bool,
};


export default RowActions;
