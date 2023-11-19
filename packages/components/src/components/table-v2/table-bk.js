/**
 * WordPress dependencies
 */
import { Button, Icon, CheckboxControl } from '@wordpress/components';
import { useMemo, useRef, useState, Fragment } from '@wordpress/element';

/**
 * External dependencies
 */
import classNames from 'classnames';
// eslint-disable-next-line import/no-extraneous-dependencies
import { get, noop } from 'lodash';

/**
 * Internal dependencies
 */
import './style.scss';

const getValueByPath = ( data, path ) => {
	if ( typeof path !== 'string' ) return null;
	return path.split( '.' ).reduce( ( pre, cur ) => ( pre || {} )[ cur ], data );
};
const defaultRender = ( row, column ) => {
	return getValueByPath( row, column.property );
};

const ASC = 'asc';
const DESC = 'desc';
function Table( props ) {
	const { columns: _columns, data: data, query = {}, emptyText, className, caption, rowKey, onChange, ...restProps } = props;
	const container = useRef( null );
	const [ tabIndex, setTabIndex ] = useState( undefined );
	const [ isScrollableRight, setIsScrollableRight ] = useState( false );
	const [ isScrollableLeft, setIsScrollableLeft ] = useState( false );
	const [ expandedRows, setExpandedRows ] = useState( [] );
	const [ selected, setSelected ] = useState( [] );
	const hasData = useMemo( () => data && data.length > 0, [ data ] );
	const columns = useMemo( () => {
		return _columns.map( ( column, index ) => {
			const align = column.align ? 'is--' + column.align : null;
			return {
				sortable: false,
				...column,
				key: column.key || index,
				width: column?.width || null,
				minWidth: column?.minWidth || null,
				property: column.property || column.key,
				render: column.render || defaultRender,
				align,
				headerAlign: column.headerAlign ? 'is--' + column.headerAlign : align,
				renderHeader: column.renderHeader || null,
			};
		} );
	}, [ _columns ] );
	const sortedBy = query.orderby || get( find( columns, { defaultSort: true } ), 'key', false );
	const sortDir = query.order || get( find( columns, { key: sortedBy } ), 'defaultOrder', DESC );
	const toggleExpand = ( row ) =>
		expandedRows.includes( row ) ? setExpandedRows( expandedRows.filter( ( r ) => r !== row ) ) : setExpandedRows( [ ...expandedRows, row ] );
	const toggleSelect = ( row ) => ( selected.includes( row ) ? setSelected( selected.filter( ( r ) => r !== row ) ) : setSelected( [ ...selected, row ] ) );
	const dispatchEvent = ( name, ...args ) => ( props[ name ] || noop )( ...args );
	const getRowKey = ( row, index ) => {
		if ( rowKey && typeof rowKey === 'function' ) {
			return rowKey( row, index );
		}
		return index;
	};
	const updateTableShadow = () => {
		const table = container.current;
		if ( table?.scrollWidth && table?.scrollHeight && table?.offsetWidth ) {
			const scrolledToEnd = table?.scrollWidth - table?.scrollLeft <= table?.offsetWidth;
			if ( scrolledToEnd && isScrollableRight ) {
				setIsScrollableRight( false );
			} else if ( ! scrolledToEnd && ! isScrollableRight ) {
				setIsScrollableRight( true );
			}
		}
		if ( table?.scrollLeft ) {
			const scrolledToStart = table?.scrollLeft <= 0;
			if ( scrolledToStart && isScrollableLeft ) {
				setIsScrollableLeft( false );
			} else if ( ! scrolledToStart && ! isScrollableLeft ) {
				setIsScrollableLeft( true );
			}
		}
	};
	const handleSelectAll = ( isSelected ) => {
		let selected = data;
		if ( ! isSelected ) {
			selected = [];
		}
		dispatchEvent( 'onChangeSelected', selected );
	};
	const renderHeader = ( column ) => {
		const { type } = column;
		if ( type === 'expand' ) {
			return column.label || '';
		}
		if ( type === 'selection' ) {
			return <CheckboxControl />;
		}

		return column.renderHeader ? column.renderHeader( column ) : column.title;
	};
	const renderCell = ( row, column, rowIndex ) => {
		const { type } = column;
		if ( type === 'expand' ) {
			return (
				<Button className="eac-table__expand-button" onClick={ () => toggleExpand( row ) }>
					<Icon size={ 16 } icon={ expandedRows.includes( row ) ? 'arrow-down-alt2' : 'arrow-right-alt2' } />
				</Button>
			);
		}

		if ( type === 'index' ) {
			return rowIndex + 1;
		}

		if ( type === 'selection' ) {
			return <CheckboxControl onChange={ () => toggleSelect( row ) } checked={ selected.includes( row ) } />;
		}

		return column.render( row, column, rowIndex );
	};
	const isRowExpanded = ( row ) => expandedRows.includes( row );

	const classes = classNames( 'eac-table', className, {
		'is--bordered': !! props.bordered,
		'has--empty-text': !! emptyText && ! data.length,
	} );

	return (
		<div className={ classes } ref={ container } tabIndex={ tabIndex } role="group" onScroll={ updateTableShadow }>
			<table>
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
						{ columns.map( ( column, colIndex ) => {
							const thProps = {
								colSpan: column.colSpan,
								rowSpan: column.rowSpan,
								className: classNames( 'eac-table__cell', column.headerAlign, column.className, {
									'is--select-column': column.type === 'select',
									'is--index-column': column.type === 'index',
									'is--expand-column': column.type === 'expand',
									'is--sortable': column.sortable,
									'is--sorted': column.sortable && sortedBy === column.key,
								} ),
								...{
									'aria-sort': column.sortable && sortedBy === column.key ? sortDir : 'none',
								},
							};
							return (
								<th key={ column.key || colIndex } role="columnheader" scope="col" { ...thProps }>
									{ column.sortable ? (
										<Button
											className="eac-table__sort-button"
											onClick={ () =>
												dispatchEvent( 'onChange', {
													orderby: column.key,
													order: sortDir === ASC ? DESC : ASC,
												} )
											}
										>
											{ renderHeader( column ) }
											<Icon size={ 16 } icon={ sortDir === ASC ? 'arrow-up-alt2' : 'arrow-down-alt2' } />
										</Button>
									) : (
										renderHeader( column )
									) }
								</th>
							);
						} ) }
					</tr>
				</thead>
				<tbody>
					{ hasData ? (
						data.map( ( row, rowIndex ) => {
							return [
								<tr
									key={ getRowKey( row, rowIndex ) }
									className={ classNames( 'eac-table__row', {
										'is-selected': row.selected,
										'is-expanded': row.expanded,
									} ) }
									style={ row.style }
								>
									{ columns.map( ( column, cellIndex ) => {
										return (
											<td
												key={ getRowKey( row, rowIndex ).toString() + cellIndex }
												className={ classNames( 'eac-table__cell', column.align, column.className, {
													'is--select-column': column.type === 'select',
													'is--index-column': column.type === 'index',
													'is--expand-column': column.type === 'expand',
													'is--sortable': column.sortable,
													'is--sorted': column.sortable && sortedBy === column.key,
												} ) }
											>
												{ renderCell( row, column, rowIndex ) }
											</td>
										);
									} ) }
								</tr>,
								isRowExpanded( row ) && (
									<tr key={ getRowKey( row, rowIndex ) + '-expanded' }>
										<td colSpan={ columns.length } className="eac-table__cell eac-table__expanded-cell">
											{ typeof row.renderExpanded === 'function' && row.renderExpanded( row ) }
										</td>
									</tr>
								),
							];
						} )
					) : (
						<tr>
							<td className="eac-table__cell" colSpan={ columns.length }>
								<div className="eac-table__empty-text">{ emptyText }</div>
							</td>
						</tr>
					) }
				</tbody>
				<tfoot></tfoot>
			</table>
		</div>
	);
}

export default Table;
