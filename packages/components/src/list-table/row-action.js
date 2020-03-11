/**
 * External dependencies
 */

import React from 'react';
import PropTypes from 'prop-types';
import { DropdownMenu } from '@wordpress/components';

const RowAction = props => {
	return (
		<div className="item-actions">
			<DropdownMenu icon="ellipsis" position="bottom right" controls={props.controls} />
		</div>
	);
};

RowAction.propTypes = {
	controls: PropTypes.array,
};

export default RowAction;
