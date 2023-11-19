/**
 * External dependencies
 */
import PropTypes from 'prop-types';
/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import { SearchControl, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { useMergedState } from '../hooks';

function Table( props ) {
	const {
		headerTitle,
		headerActions,
		query,
		data,
		search,
		reload,
		toolbar,
		toolBarRender,
		bulkActions,
		onChange,
		style,
		className,
		...rest
	} = props;

	const [ mergedQuery, setMergedQuery ] = useMergedState( {
		defaultValue: {},
		value: query,
		onChange,
	} );

	// HEADER SECTION.
	const headerTitleNode = useMemo( () => {
		if ( ! headerTitle ) {
			return null;
		}
		return <div className="eac-table__section-col eac-table__section-col--left">{ headerTitle }</div>;
	}, [ headerTitle ] );

	const headerActionsNode = useMemo( () => {
		if ( ! headerActions ) {
			return null;
		}
		return <div className="eac-table__section-col eac-table__section-col--right">{ headerActions }</div>;
	}, [ headerActions ] );

	const headerDom = useMemo( () => {
		if ( ! headerTitleNode && ! headerActionsNode ) {
			return null;
		}
		return (
			<div className="eac-table__section eac-table__section--header">
				{ [ headerTitleNode, headerActionsNode ] }
			</div>
		);
	}, [ headerTitleNode, headerActionsNode ] );

	// TOOLBAR SECTION.
	const searchNode = useMemo( () => {
		return search === false ? null : (
			<SearchControl
				className="eac-table__search"
				value={ mergedQuery?.search || '' }
				onChange={ ( value ) => {
					setMergedQuery( ( prev ) => ( {
						...prev,
						search: value,
					} ) );
				} }
			/>
		);
	}, [ search, mergedQuery?.search, setMergedQuery ] );

	const reloadNode = useMemo( () => {
		return reload === false ? null : (
			<Button
				className="eac-table__reload"
				onClick={ () => {
					setMergedQuery( ( prev ) => ( {
						...prev,
						reload: true,
					} ) );
				} }
			>
				{ __( 'Reload', 'wp-ever-accounting' ) }
			</Button>
		);
	}, [ reload, setMergedQuery ] );

	const bulkActionsNode = () => null;

	return (
		<div className="eac-table">
			{ headerDom }
			<div className="eac-table-container">Table</div>
		</div>
	);
}

Table.propTypes = {
	// Table title
	headerTitle: PropTypes.oneOfType( [ PropTypes.node, PropTypes.bool ] ),
	// Header actions.
	headerActions: PropTypes.oneOfType( [ PropTypes.node, PropTypes.bool ] ),
	// Current query object of the table. This object contains the current page, page size, sorting, and filtering information.
	query: PropTypes.object,
	// Table data. The data is an array of objects.
	data: PropTypes.arrayOf( PropTypes.object ),
	// An array of columns, as objects.
	columns: PropTypes.arrayOf(
		PropTypes.shape( {
			// Title of this column
			title: PropTypes.string,
			// column's key. If you need to use the onFilterChange event, you need this attribute to identify which column is being filtered.
			key: PropTypes.string,
			// Display field of the data record, support nest path by string array
			dataIndex: PropTypes.string,
			// type of the column. If set to selection, the column will display checkbox. If set to index, the column will display index of the row (staring from 1). If set to expand, the column will display expand icon.
			type: PropTypes.oneOf( [ 'selection', 'expandable' ] ),
			//alignment of the table cell. If omitted, the value of the above align attribute will be applied.
			align: PropTypes.oneOf( [ 'left', 'center', 'right' ] ),
			//alignment of the table header. If omitted, the value of the above align attribute will be applied.
			headerAlign: PropTypes.oneOf( [ 'left', 'center', 'right' ] ),
			//classname
			className: PropTypes.string,
			//Span of this column's title
			colSpan: PropTypes.number,
			//is sortable or not
			sortable: PropTypes.bool,
			//column width
			width: PropTypes.number,
			//with width has a fixed width, while columns with minWidth
			minWidth: PropTypes.number,
			// Renderer of the table cell. The return value should be a ReactNode.
			render: PropTypes.func,
			//render function for table header of this column
			renderHeader: PropTypes.func,
			//function that determines if a certain row can be selected, works when type is 'selection'
			disabled: PropTypes.oneOfType( [ PropTypes.func, PropTypes.bool ] ),
		} )
	),
	//Whether to display the search form, when the object is passed in, it is the configuration of the search form.
	search: PropTypes.bool,
	// Whether to display the rea
	reload: PropTypes.bool,
	// Whether to display the toolbar, when the object is passed in, it is the configuration of the toolbar.
	toolbar: PropTypes.oneOfType( [ PropTypes.bool, PropTypes.object ] ),
	// Rendering toolbar supports returning a DOM array and automatically adds margin-right to the last element.
	toolbarRender: PropTypes.oneOfType( [ PropTypes.func, PropTypes.bool ] ),
};

export default Table;
