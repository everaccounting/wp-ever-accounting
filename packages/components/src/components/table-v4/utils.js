/**
 * External dependencies
 */
import { get } from 'lodash';

/**
 * WordPress dependencies
 * @param {Object} columns The columns to normalize.
 */
export function normalizeColumns( columns ) {
	return columns.map(
		( column, index ) => {
			const align = column.align ? 'is--' + column.align : null;
			return {
				sortable: false,
				filterable: false,
				...column,
				key: column.key || index,
				width: column?.width || null,
				minWidth: column?.minWidth || null,
				property: column.property || column.key,
				render: column.render || defaultRender,
				align,
				headerAlign: column.headerAlign ?? align,
				renderHeader: column.renderHeader || null,
			};
		},
		[ columns ]
	);
}

export const getValueByPath = ( data, path ) => {
	if ( typeof path !== 'string' ) return null;
	return path.split( '.' ).reduce( ( pre, cur ) => ( pre || {} )[ cur ], data );
};

const defaultRender = ( row, column ) => getValueByPath( row, column.property );
