/**
 * WordPress dependencies
 */
import { Button, Icon, CheckboxControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
/**
 * External dependencies
 */
import classNames from 'classnames';

import FilterPanel from "./filter-panel";

function TableHeader( props ) {
	const { columns, isLoading, onSort, query, isAllSelected, onSelectAll } = props;

	const renderHeader = ( column ) => {
		const { type } = column;
		if ( type === 'expandable' ) {
			return column.label || '';
		}
		if ( type === 'selection' ) {
			return <CheckboxControl disabled={ isLoading } checked={ isAllSelected } onChange={ onSelectAll } size='small'/>;
		}
		return column.renderHeader ? column.renderHeader( column ) : column.title;
	};

	return (
		<tr>
			{ columns.map( ( column, index ) => {
				const Cell = column.type === 'selection' ? 'td' : 'th';
				const classes = classNames( 'eac-table__column', column.className, {
					'eac-table__cell': true,
					'eac-table__column--sortable': column.sortable,
					// 'eac-table__column--sorted': column.sortable && orderby === column.key,
					'eac-table__cell--selection': column.type === 'selection',
					'eac-table__cell--expandable': column.type === 'expandable',
					'eac-table__cell--center': 'center' === column.headerAlign,
					'eac-table__cell--right': 'right' === column.headerAlign,
				} );
				return (
					<Cell
						key={ column.key || index }
						colSpan={ column.colSpan }
						rowSpan={ column.rowSpan }
						className={ classes }
						scope="col"
						role="columnheader"
						//aria-sort={ column.sortable && orderby === column.key ? order : 'none' }
					>
						{ column.sortable ? (
							<Button
								className="eac-table__sort-button"
								onClick={ () =>
									onSort( {
										orderby: column.key,
										order: query?.orderby === column.key && query?.order === 'asc' ? 'desc' : 'asc',
									} )
								}
								aria-label={ sprintf(
									/* translators: %s: column label */
									__( 'Sort by %s', 'wp-ever-accounting' ),
									column.label
								) }
								aria-disabled={ isLoading }
								disabled={ isLoading }
							>
								{ renderHeader( column ) }
								<span className="eac-table__sort-icon">
									<Icon icon={ query?.orderby === column.key && query?.order === 'asc' ? 'arrow-up-alt2' : 'arrow-down-alt2' } size={ 16 } />
								</span>
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

export default TableHeader;
