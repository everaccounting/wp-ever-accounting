/**
 * External dependencies
 */

import React from 'react';

/**
 * Internal dependencies
 */

const isSelected = (selected, id) => selected.indexOf(parseInt(id, 10)) !== -1;
const TableContent = props => {
	const { rows, status, selected, row, search } = props;
	const isLoading = status === 'STATUS_IN_PROGRESS';
	return <tbody>{rows.map((item, pos) => row(item, pos, isSelected(selected, item.id), isLoading, search))}</tbody>;
};

export default TableContent;
