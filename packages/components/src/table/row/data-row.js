/**
 * External dependencies
 */

import React from 'react';

/**
 * Internal dependencies
 */

const isSelected = ( selected, id ) => selected.indexOf( parseInt(id, 10) ) !== -1;

const getRowStatus = ( status, selected, item ) => ( {
	isLoading: status === "STATUS_IN_PROGRESS",
	isSelected: isSelected( selected, item.id ),
} );


const TableContent = props => {
	const { rows, status, selected, row, search } = props;
	return (
		<tbody>
			{ rows.map( ( item, pos ) => row( item, pos, getRowStatus( status, selected, item ), search ) ) }
		</tbody>
	);
};

export default TableContent;
