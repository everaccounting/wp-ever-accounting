/**
 * External dependencies
 */
import { get } from 'lodash';
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import { Button, Icon, CheckboxControl } from '@wordpress/components';

function Cell( props ) {
	const { index, column, query, row, onSelectRow, isSelected, onRowExpanded, isExpanded } = props;

	const renderCell = useMemo( () => {
		if ( 'selectable' === column.type ) {
			return (
				<CheckboxControl
					disabled={ column.disabled }
					onChange={ ( checked ) => onSelectRow( checked, row ) }
					checked={ isSelected }
				/>
			);
		}

		if ( 'expandable' === column.type ) {
			return (
				<Button
					className="eac-table__expand-button"
					disabled={ column.disabled }
					onClick={ () => onRowExpanded( row ) }
				>
					<Icon size={ 16 } icon={ isExpanded ? 'arrow-down-alt2' : 'arrow-right-alt2' } />
				</Button>
			);
		}

		if ( column.render ) {
			return column.render( row, column, index );
		}

		return get( row, column.dataIndex, '' );
	}, [ column, row, isSelected, onSelectRow, isExpanded, onRowExpanded, index ] );

	const classes = classNames( 'eac-table__cell', {
		'eac-table__cell--expandable': column.type === 'expandable',
		'eac-table__cell--selectable': column.type === 'selectable',
		'eac-table__cell--center': column.align === 'center',
		'eac-table__cell--right': column.align === 'right',
		'eac-table__cell--ellipsis': !! column.ellipsis,
		'eac-table__cell--sorted': column.sortable && query?.orderby === column.key,
	} );

	return (
		<td
			key={ index }
			className={ classes }
			colSpan={ column.colSpan }
			rowSpan={ column.rowSpan }
			aria-colindex={ index + 1 }
		>
			{ renderCell }
		</td>
	);
}

export default Cell;
