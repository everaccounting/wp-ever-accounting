/**
 * Internal dependencies
 */
import Input from '../input';
/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { Button, Icon } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

function Header( { isLoading, columns, orderby, order, onSort, isAllSelected, onSelectAll } ) {
	const renderCell = ( column ) => {
		if ( 'selectable' === column.type ) {
			return (
				<Input.Checkbox
					disabled={ column.disabled || isLoading }
					onChange={ ( checked ) => onSelectAll( checked ) }
					checked={ isAllSelected }
				/>
			);
		}

		if ( 'expandable' === column.type ) {
			return null;
		}

		return column.renderHeader?.( column ) || column.title || '';
	};
	return (
		<tr>
			{ columns.map( ( column, index ) => {
				const cellNode = renderCell( column );
				const Cell = [ 'selectable', 'expandable' ].includes( column.type ) ? 'td' : 'th';
				const cellProps = {
					key: column.key || index,
					colSpan: column.colSpan,
					rowSpan: column.rowSpan,
					className: classNames( 'eac-table__column', {
						'eac-table__column--sortable': column.sortable,
						'eac-table__column--expandable': column.type === 'expandable',
						'eac-table__column--selectable': column.type === 'selectable',
						'eac-table__column--center': column.align === 'center',
						'eac-table__column--right': column.align === 'right',
						'eac-table__column--ellipsis': !! column.ellipsis,
						'eac-table__column--sorted': column.sortable && orderby === column.key,
					} ),
					scope: 'col',
					role: 'columnheader',
					...( column.sortable && {
						'aria-sort': orderby === column.key ? order : 'none',
					} ),
				};
				return (
					<Cell key={ column.key || index } { ...cellProps }>
						{ column.sortable ? (
							<Button
								className="eac-table__sort-button"
								onClick={ () =>
									onSort( {
										orderby: column.key,
										order: orderby === column.key && order === 'asc' ? 'desc' : 'asc',
									} )
								}
								aria-disabled={ isLoading }
								disabled={ isLoading }
							>
								{ cellNode }
								<span className="eac-table__sort-icon">
									<Icon
										icon={
											orderby === column.key && order === 'asc'
												? 'arrow-up-alt2'
												: 'arrow-down-alt2'
										}
										size={ 16 }
									/>
								</span>
							</Button>
						) : (
							cellNode
						) }
					</Cell>
				);
			} ) }
		</tr>
	);
}

export default Header;
