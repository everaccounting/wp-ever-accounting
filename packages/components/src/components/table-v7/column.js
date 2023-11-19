/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import Input from '../input';
/**
 * WordPress dependencies
 */
import { Button, Icon } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

function Column( props ) {
	const { index, query, column, loading, onSort, isAllSelected, onSelectAll } = props;
	const renderColumn = () => {
		if ( 'selectable' === column.type ) {
			return (
				<Input.Checkbox
					disabled={ column.disabled || loading }
					onChange={ ( checked ) => onSelectAll( checked ) }
					checked={ isAllSelected }
				/>
			);
		}

		if ( 'expandable' === column.type ) {
			return null;
		}

		if ( column.renderHeader ) {
			return column.renderHeader( column, index );
		}

		return column.title || '';
	};

	const classes = classNames( 'eac-table__column', {
		'eac-table__column--sortable': column.sortable,
		'eac-table__column--expandable': column.type === 'expandable',
		'eac-table__column--selectable': column.type === 'selectable',
		'eac-table__column--center': column.align === 'center',
		'eac-table__column--right': column.align === 'right',
		'eac-table__column--ellipsis': !! column.ellipsis,
		'eac-table__column--sorted': column.sortable && query?.orderby === column.key,
	} );
	const cellNode = renderColumn( column );
	const Col = [ 'selectable', 'expandable' ].includes( column.type ) ? 'td' : 'th';

	return (
		<Col
			key={ column.key || index }
			colSpan={ column.colSpan }
			rowSpan={ column.rowSpan }
			className={ classes }
			scope="col"
			role="columnheader"
			{ ...( column.sortable && { 'aria-sort': query?.orderby === column.key ? query?.order : 'none' } ) }
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
						column.title
					) }
					aria-disabled={ loading }
					disabled={ loading }
				>
					{ cellNode }
					<span className="eac-table__sort-icon">
						<Icon
							icon={
								query?.orderby === column.key && query?.order === 'asc'
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
		</Col>
	);
}

export default Column;
