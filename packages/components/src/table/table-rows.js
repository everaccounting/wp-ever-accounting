/**
 * WordPress dependencies
 */
import { Button, CheckboxControl, Icon } from '@wordpress/components';
/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import { getValueByPath } from './utils';
function TableRows( props ) {
	const { columns, dataSource, rowKey, rowStyle, rowClassName, onToggleExpanded, expandedRows = [], selectedRows = [], onSelectChange } = props;
	const isRowExpanded = ( row ) => expandedRows.includes( row );

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

	const renderCell = ( row, column, index ) => {
		const { type } = column;
		if ( type === 'selection' ) {
			return <CheckboxControl onChange={ ( checked ) => onSelectChange( checked, row ) } checked={ selectedRows.includes( row ) } />;
		} else if ( type === 'expandable' ) {
			return (
				<Button className="eac-table__expand-button" onClick={ () => onToggleExpanded( row ) }>
					<Icon size={ 16 } icon={ isRowExpanded( row ) ? 'arrow-down-alt2' : 'arrow-right-alt2' } />
				</Button>
			);
		}

		return column.render( row, column, index );
	};

	return (
		<>
			{ dataSource.map( ( row, index ) => {
				const classes = classNames( 'eac-table__row', getRowClass( row, index ) );
				const rowIndex = getRowKey( row, index );
				return [
					<tr
						key={ rowIndex }
						style={ getRowStyle( row, index ) }
						className={ classes }
						onClick={ props.onRowClick ? () => props.onRowClick( row, index ) : null }
						onDoubleClick={ props.onRowDoubleClick ? () => props.onRowDoubleClick( row, index ) : null }
						onMouseEnter={ props.onRowMouseEnter ? () => props.onRowMouseEnter( row, index ) : null }
						onMouseLeave={ props.onRowMouseLeave ? () => props.onRowMouseLeave( row, index ) : null }
						onContextMenu={ props.onRowContextMenu ? () => props.onRowContextMenu( row, index ) : null }
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
									onClick={ column.onClick ? () => column.onClick( row, column, index ) : null }
									onDoubleClick={ column.onDoubleClick ? () => column.onDoubleClick( row, column, index ) : null }
									onMouseEnter={ column.onMouseEnter ? () => column.onMouseEnter( row, column, index ) : null }
									onMouseLeave={ column.onMouseLeave ? () => column.onMouseLeave( row, column, index ) : null }
								>
									{ renderCell( row, column, index ) }
								</td>
							);
						} ) }
					</tr>,
					isRowExpanded( row ) && (
						<tr key={ rowIndex + '-expanded' } className="eac-table__row eac-table__row--expanded">
							<td colSpan={ columns.length } className="eac-table__cell eac-table__expanded-cell">
								{ typeof props.renderExpanded === 'function' && props.renderExpanded( row ) }
							</td>
						</tr>
					),
				];
			} ) }
		</>
	);
}

export default TableRows;
