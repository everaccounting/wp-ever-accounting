/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import { SearchControl, Button, Icon } from '@wordpress/components';
/**
 * Internal dependencies
 */
import Placeholder from '../placeholder';
import { getValueByPath } from '../table-v4/utils';
/**
 * Internal dependencies
 */

function Table( props ) {
	const {
		className,
		caption,
		columns,
		data,
		emptyMessage,
		isLoading,
		rowKey,
		rowStyle,
		rowClassName,
		children,
		...rest
	} = props;
	const hasData = useMemo( () => data && data.length > 0, [ data ] );

	const getRowKey = ( row, index ) => {
		if ( typeof rowKey === 'string' ) {
			return getValueByPath( row, rowKey );
		} else if ( typeof rowKey === 'function' ) {
			return rowKey( row );
		}
		return index;
	};

	const getRowStyle = ( row, index ) => {
		if ( typeof rowStyle === 'function' ) {
			return rowStyle( row, index );
		}
		return rowStyle;
	};

	const getRowClass = ( row, index ) => {
		if ( typeof rowClassName === 'function' ) {
			return rowClassName( row, index );
		}
		return rowClassName;
	};

	return (
		<table className={ className } { ...rest }>
			{ caption && <caption className="eac-table__caption">{ caption }</caption> }
			<colgroup>
				{ columns.map( ( column, index ) => (
					<col
						width={ column.width ? column.width : null }
						style={ {
							minWidth: column.minWidth ? column.minWidth : null,
							maxWidth: column.maxWidth ? column.maxWidth : null,
							width: column.width ? column.width : null,
						} }
						key={ index }
					/>
				) ) }
			</colgroup>
			<thead>
				<tr>
					{ columns.map( ( column, index ) => {
						const Cell = column.type === 'selection' ? 'td' : 'th';
						const classes = classNames( 'eac-table__column', column.className, {
							'eac-table__cell': true,
							'eac-table__column--sortable': column.sortable,
							'eac-table__cell--selection': column.type === 'selection',
							'eac-table__cell--expandable': column.type === 'expandable',
							'eac-table__cell--center': 'center' === column.headerAlign,
							'eac-table__cell--right': 'right' === column.headerAlign,
						} );
						const cellNode = column.renderHeader ? column.renderHeader( column ) : column.title;
						return (
							<Cell
								key={ column.key || index }
								colSpan={ column.colSpan }
								rowSpan={ column.rowSpan }
								className={ classes }
								scope="col"
								role="columnheader"
							>
								{ cellNode }
							</Cell>
						);
					} ) }
				</tr>
			</thead>
			<tbody>
				{ ! hasData && ! isLoading && (
					<tr>
						<td className="eac-table__cell eac-table__cell--empty" colSpan={ columns.length }>
							{ emptyMessage }
						</td>
					</tr>
				) }
				{ isLoading &&
					Array.from( { length: 10 }, ( _, index ) => (
						<tr key={ index }>
							{ columns.map( ( column, i ) => (
								<td key={ i } className="eac-table__cell eac-table__cell--loading">
									Loading...
								</td>
							) ) }
						</tr>
					) ) }

				{ hasData &&
					data.map( ( row, index ) => {
						const classes = classNames( 'eac-table__row', {
							'eac-table__row--selected': row.selected,
							'eac-table__row--expandable': row.expandable,
						} );
						return (
							<tr key={ index } className={ classes }>
								{ columns.map( ( column, i ) => {
									const classes = classNames( 'eac-table__row', getRowClass( row, index ) );
									const rowIndex = getRowKey( row, index );
									return [
										<tr
											key={ rowIndex }
											style={ getRowStyle( row, index ) }
											className={ classes }
											onClick={ props.onRowClick ? () => props.onRowClick( row, index ) : null }
											onDoubleClick={
												props.onRowDoubleClick
													? () => props.onRowDoubleClick( row, index )
													: null
											}
											onMouseEnter={
												props.onRowMouseEnter ? () => props.onRowMouseEnter( row, index ) : null
											}
											onMouseLeave={
												props.onRowMouseLeave ? () => props.onRowMouseLeave( row, index ) : null
											}
											onContextMenu={
												props.onRowContextMenu
													? () => props.onRowContextMenu( row, index )
													: null
											}
										>
											{ columns.map( ( column, i ) => {
												const cellClasses = classNames( 'eac-table__cell', column.className, {
													'eac-table__cell--selection': column.type === 'selection',
													'eac-table__cell--expandable': column.type === 'expandable',
													'eac-table__cell--center': 'center' === column.align,
													'eac-table__cell--right': 'right' === column.align,
												} );
												return (
													<td
														key={ column.property || i }
														className={ cellClasses }
														onClick={
															column.onClick
																? () => column.onClick( row, column, index )
																: null
														}
														onDoubleClick={
															column.onDoubleClick
																? () => column.onDoubleClick( row, column, index )
																: null
														}
														onMouseEnter={
															column.onMouseEnter
																? () => column.onMouseEnter( row, column, index )
																: null
														}
														onMouseLeave={
															column.onMouseLeave
																? () => column.onMouseLeave( row, column, index )
																: null
														}
													>
														{ renderCell( row, column, index ) }
													</td>
												);
											} ) }
										</tr>,
										isRowExpanded( row ) && (
											<tr
												key={ rowIndex + '-expanded' }
												className="eac-table__row eac-table__row--expanded"
											>
												<td
													colSpan={ columns.length }
													className="eac-table__cell eac-table__expanded-cell"
												>
													{ typeof props.renderExpanded === 'function' &&
														props.renderExpanded( row ) }
												</td>
											</tr>
										),
									];
								} ) }
							</tr>
						);
					} ) }
			</tbody>
		</table>
	);
}

export default Table;
