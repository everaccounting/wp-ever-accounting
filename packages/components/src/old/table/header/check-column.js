/**
 * External dependencies
 */

import React from 'react';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';

const CheckColumn = props => {
	const { onSetAllSelected, isDisabled, isSelected } = props;

	return (
		<td className="manage-column column-cb check-column-red">
			<label className="screen-reader-text">{__('Select All')}</label>
			<input type="checkbox" disabled={isDisabled} checked={isSelected} onChange={onSetAllSelected} />
		</td>
	);
};

CheckColumn.propTypes = {
	isDisabled: PropTypes.bool.isRequired,
	isSelected: PropTypes.bool.isRequired,
	onSetAllSelected: PropTypes.func.isRequired,
};

export default CheckColumn;