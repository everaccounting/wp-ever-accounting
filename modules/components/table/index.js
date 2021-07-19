/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { noop } from 'lodash';
/**
 * Internal dependencies
 */
import TableHeader from './header';
import TableBody from './body';
import { normalizeColumns } from './utils';

import './style.scss';

function Table( props ) {
	const {
		className,
		rows = [],
		query = {},
		isRequesting,
		selected = [],
		saving = [],
	} = props;
	const { orderby, order } = query;
	const sort = { orderby, order };
	const [ columns, setColumns ] = useState( [] );

	const handleSelectAll = ( isSelected ) => {
		let selected = rows;
		if ( ! isSelected ) {
			selected = [];
		}
		dispatchEvent( 'onChangeSelected', selected );
	};

	useEffect( () => {
		setColumns( normalizeColumns( props.columns ) );
	}, [ props.columns, rows ] );

	const handleSelection = ( row, isSelected ) => {
		const selectedRows = selected.slice();
		const rowIndex = selectedRows.indexOf( row );
		if ( isSelected !== undefined ) {
			if ( isSelected ) {
				// eslint-disable-next-line no-unused-expressions
				rowIndex === -1 && selectedRows.push( row );
			} else {
				// eslint-disable-next-line no-unused-expressions
				rowIndex !== -1 && selectedRows.splice( rowIndex, 1 );
			}
		} else {
			// eslint-disable-next-line no-unused-expressions
			rowIndex === -1
				? selectedRows.push( row )
				: selectedRows.splice( rowIndex, 1 );
		}

		dispatchEvent( 'onChangeSelected', selectedRows );
	};

	const handleSort = ( sort ) => {
		dispatchEvent( 'onSort', sort );
	};

	const isAllSelected =
		( rows && rows.length !== 0 && rows.length === selected.length ) ===
		true;

	const dispatchEvent = ( name, ...args ) => {
		const fn = props[ name ];
		if ( fn ) {
			fn( ...args );
		}
	};

	const classes = classNames(
		'wp-list-table',
		'widefat',
		'fixed',
		'striped',
		'items',
		'ea-list-table',
		className
	);

	return (
		<table className={ classes }>
			<thead>
				<TableHeader
					className="table-header"
					columns={ columns }
					sort={ sort }
					onSort={ handleSort }
					isAllSelected={ isAllSelected }
					isRequesting={ isRequesting }
					onSelectAll={ handleSelectAll }
				/>
			</thead>

			<tbody>
				<TableBody
					rows={ rows }
					columns={ columns }
					isRequesting={ isRequesting }
					selected={ selected }
					saving={ saving }
					onSelect={ handleSelection }
				/>
			</tbody>

			<tfoot>
				<TableHeader
					className="table-header"
					columns={ columns }
					sort={ sort }
					setSort={ handleSort }
					isAllSelected={ isAllSelected }
					isRequesting={ isRequesting }
					onSelectAll={ handleSelectAll }
				/>
			</tfoot>
		</table>
	);
}

Table.propTypes = {
	// Additional CSS classes.
	className: PropTypes.string,
	// An array of columns, as objects.
	columns: PropTypes.arrayOf(
		PropTypes.shape( {
			//type of the column. If set to selection, the column will display checkbox
			type: PropTypes.string,
			//column label
			label: PropTypes.string,
			//field name. You can also use its alias: property
			prop: PropTypes.string,
			//column width
			width: PropTypes.number,
			//with width has a fixed width, while columns with minWidth
			minWidth: PropTypes.number,
			//custom render Function
			render: PropTypes.func,
			//render function for table header of this column
			renderHeader: PropTypes.func,
			//is sortable or not
			sortable: PropTypes.bool,
			// is the column primary
			isPrimary: PropTypes.bool,
			//alignment
			align: PropTypes.oneOf( [ 'left', 'center', 'right' ] ),
			//header alignment
			headerAlign: PropTypes.oneOf( [ 'left', 'center', 'right' ] ),
			//classname
			className: PropTypes.string,

			//column actions
			actions: PropTypes.arrayOf(
				PropTypes.shape( {
					label: PropTypes.string,
					onClick: PropTypes.func,
				} )
			),
		} )
	),
	//on sort change
	onSort: PropTypes.func,
	//table data
	rows: PropTypes.arrayOf( PropTypes.object ),
	//Selected items
	selected: PropTypes.array,
	//Saving items
	saving: PropTypes.array,
	//weather to show header or not
	showHeader: PropTypes.bool,
	// The query string represented in object form.
	query: PropTypes.object,
	//on select item
	onChangeSelected: PropTypes.func,
	//is Requesting
	isRequesting: PropTypes.bool,
};

Table.defaultProps = {
	onSort: noop,
	rows: [],
	saving: [],
	query: {},
	onSelect: noop,
	onToggleSelect: noop,
};

export default Table;
