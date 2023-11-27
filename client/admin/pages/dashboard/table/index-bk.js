/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
/**
 * External dependencies
 */
import classNames from 'classnames';
import { useMergedState, Table } from '@eac/components';
/**
 * Internal dependencies
 */
import './style.scss';

function PropTable( props ) {
	const {
		query,
		data,
		search, // Whether to display the search form, when the object is passed in, it is the configuration of the search form.
		toolbar, // Whether to display the toolbar, when the object is passed in, it is the configuration of the toolbar.
		options, // table toolbar, not displayed when set to false
		style,
		className,
		...rest
	} = props;

	const columns = useMemo( () => {
		return props.columns.map( ( column, index ) => {
			return {
				...column,
				key: column.key || index,
				title: column.title || null,
				tooltip: column.tooltip || null,
				ellipsis: column.ellipsis || null,
				sortable: column.sortable || false,
				width: column?.width || null,
				minWidth: column?.minWidth || null,
				property: column.property || column.key,
				render: column.render || null,
				align: column.align ?? null,
				visible: column.visible ?? true,
				headerAlign: column.headerAlign ?? null,
				renderHeader: column.renderHeader || null,
			};
		} );
	}, [ props.columns ] );

	// Selected rows and selected rows change handler.
	const [ selectedRows, setSelectedRows ] = useMergedState( [], {
		value: props.selectedRows,
		onChange: props.onSelectedRowsChange,
	} );
	const isAllSelected = useMemo( () => selectedRows.length === data.length, [ selectedRows, data ] );
	const onSelectAll = ( checked ) => {
		setSelectedRows( checked ? data : [] );
		dispatchEvent( 'onSelectAll', checked );
		dispatchEvent( 'onSelectChange', checked ? data : [] );
	};
	const onSelectChange = ( checked, row ) => {
		const selected = selectedRows.includes( row ) ? selectedRows.filter( ( r ) => r !== row ) : [ ...selectedRows, row ];
		setSelectedRows( selected );
		dispatchEvent( 'onSelectChange', selected );
	};

	// Expanded rows and expanded rows change handler.
	const [ expandedRows, setExpandedRows ] = useMergedState( [], {
		value: props.expandedRows,
		onChange: props.onExpandedRowsChange,
	} );
	const isAllExpanded = useMemo( () => expandedRows.length === data.length, [ expandedRows, data ] );
	const onToggleExpanded = ( row ) => {
		const expanded = expandedRows.includes( row ) ? expandedRows.filter( ( r ) => r !== row ) : [ ...expandedRows, row ];
		setExpandedRows( expanded );
		dispatchEvent( 'onExpand', expanded );
	};

	// Pagination.
	const classes = classNames( 'eac-pro-table', className );

	return (
		<div className={ classes }>
			<Table dataSource={ data } columns={ columns } { ...rest } />
		</div>
	);
}

export default PropTable;
