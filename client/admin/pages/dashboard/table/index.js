/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import { SearchControl, Button } from '@wordpress/components';
/**
 *
 * External dependencies
 */
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { useMergedState, Input, Pagination } from '@eac/components';
/**
 * Internal dependencies
 */
import './style.scss';

function PropTable( props ) {
	const {
		headerTitle,
		headerActions,
		query,
		data,
		search,
		toolbar,
		toolBarRender,
		options,
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

	const headerDom =
		! headerTitle && ! headerActions ? null : (
			<div className="eac-table__section eac-table__section--header">
				{ headerTitle && (
					<div className="eac-table__section-col eac-table__section-col--left">
						{ headerTitle }
					</div>
				) }
				{ headerActions && (
					<div className="eac-table__section-col eac-table__section-col--right">
						{ headerActions }
					</div>
				) }
			</div>
		);

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

	// const realodNode = useMemo( () => {
	// 	return reload === false ? null : (
	// 		<Button
	// 			className="eac-table__reload"
	// 			onClick={ () => {
	// 				setMergedQuery( ( prev ) => ( {
	// 					...prev,
	// 					reload: true,
	// 				} ) );
	// 			} }
	// 		>
	// 			Reload
	// 		</Button>
	// 	);
	// }, [ reload, setMergedQuery ] );

	const toolbarDom =
		toolBarRender === false ? null : (
			<div className="eac-table__section eac-table__section--toolbar">
				<Button isSecondary={ true } icon="filter" disabled>
					Actions
				</Button>
				{ searchNode }
				<Button isTertiary={ true } icon="filter" />
				<Button isTertiary={ true } icon="image-rotate" />
				<Button isTertiary={ true } icon="admin-generic" />
			</div>
		);

	return (
		<div className="eac-table">
			{ headerDom }
			{ toolbarDom }
			{/*<div className="eac-table__section eac-table__section--filter">*/}
			{/*	Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad alias doloribus eos id impedit in maxime odit quis similique tempora?*/}
			{/*</div>*/}
			{/*<div className="eac-table__section eac-table__section--alert">*/}
			{/*	Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad alias doloribus eos id impedit in maxime odit quis similique tempora?*/}
			{/*</div>*/}
			{ data && (
				<div className="eac-table-container">
					<table>
						<thead>
							<tr>
								<th className="eac-table__cell eac-table__column">
									Name
								</th>
								<th className="eac-table__cell eac-table__column">
									Price
								</th>
								<th className="eac-table__cell eac-table__column">
									Cost
								</th>
								<th className="eac-table__cell eac-table__column">
									Status
								</th>
							</tr>
						</thead>
						<tbody>
							{ data.map( ( item, index ) => {
								return (
									<tr key={ index }>
										<td className="eac-table__cell">
											{ item.name }
										</td>
										<td className="eac-table__cell">
											{ item.price }
										</td>
										<td className="eac-table__cell">
											{ item.cost }
										</td>
										<td className="eac-table__cell">
											{ item.status }
										</td>
									</tr>
								);
							} ) }
						</tbody>
					</table>
				</div>
			) }
			<div className="eac-table__section eac-table__section--pagination">
				<Pagination total={ 100 } />
			</div>
		</div>
	);
}

PropTable.propTypes = {
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
			property: PropTypes.oneOfType( [
				PropTypes.string,
				PropTypes.arrayOf( PropTypes.string ),
			] ),
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

export default PropTable;
