/**
 * WordPress dependencies
 */
import { Button, Icon, CheckboxControl } from '@wordpress/components';

/**
 * External dependencies
 */
import classNames from 'classnames';

function Header( props ) {
	const { className, columns, sort, onSort, isAllSelected = false, isLoading = false, onSelectAll } = props;
	const { orderby, order } = sort;

	const renderHeader = ( column ) => {
		const { type } = column;
		if ( type === 'expand' ) {
			return column.label || '';
		}

		if ( type === 'index' ) {
			return column.label || '#';
		}

		if ( type === 'selection' ) {
			return (
				<>
					<label className="screen-reader-text">{ __( 'Select All', 'wp-ever-accounting' ) }</label>; return{ ' ' }
					<CheckboxControl disabled={ isLoading } checked={ isAllSelected } onChange={ onSelectAll } />;
				</>
			);
		}

		return column.renderHeader ? column.renderHeader( column ) : column.label;
	};

	return (
		<tr className={ className }>
			{ columns.map( ( column, index ) => {
				const Cell = column.type === 'selection' ? 'td' : 'th';
				const classes = classNames( 'eac-table__cell', column.headerAlign, column.className, {
					'is--select-column': column.type === 'select',
					'is--index-column': column.type === 'index',
					'is--expand-column': column.type === 'expand',
					'is--sortable': column.sortable,
					'is--sorted': column.sortable && orderby === column.key,
				} );

				return (
					<Cell
						key={ column.key || index }
						role="columnheader"
						scope="col"
						colSpan={ column.colSpan }
						rowSpan={ column.rowSpan }
						className={ classes }
						aria-sort={ column.sortable && orderby === column.key ? order : 'none' }
					>
						{ column.sortable ? (
							<Button
								className="eac-table__sort-button"
								onClick={ () =>
									onSort( {
										orderby: column.key,
										order: order === 'asc' ? 'desc' : 'asc',
									} )
								}
							>
								{ renderHeader( column ) }
								<Icon size={ 16 } icon={ order === 'asc' ? 'arrow-up-alt2' : 'arrow-down-alt2' } />
							</Button>
						) : (
							renderHeader( column )
						) }
					</Cell>
				);
			} ) }
		</tr>
	);
}

export default Header;
