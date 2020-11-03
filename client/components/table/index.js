import {useState, useEffect} from "@wordpress/element";
import PropTypes from 'prop-types';
import classNames from "classnames";

import TableHeader from './header';
import TableBody from './body';
import {normalizeColumns} from './utils';

import './style.scss';

function Table(props) {
	const {className, data, defaultSort = {}, isLoading, noResult} = props;
	const [columns, setColumns] = useState([]);
	const [sort, setSort] = useState(defaultSort);
	const [selected, setSelected] = useState([]);

	useEffect(() => {
		setColumns(normalizeColumns(props.columns));
	}, [props.columns]);

	const handleSelectAll = (isSelected) => {
		let selected = data;
		if (!isSelected) {
			selected = [];
		}
		setSelected(selected);
		dispatchEvent('onSelectAll', selected);
		dispatchEvent('onSelectChange', selected);
	}

	const handleSelection = (row, isSelected) => {
		const selectedRows = selected.slice();
		const rowIndex = selectedRows.indexOf(row);
		if (isSelected !== undefined) {
			if (isSelected) {
				rowIndex === -1 && selectedRows.push(row);
			} else {
				rowIndex !== -1 && selectedRows.splice(rowIndex, 1);
			}
		} else {
			rowIndex === -1 ? selectedRows.push(row) : selectedRows.splice(rowIndex, 1)
		}

		setSelected(selectedRows);
		dispatchEvent('onSelect', selected, row);
		dispatchEvent('onSelectChange', selected);
	}

	const handleSort = (sort) => {
		setSort(sort);
		dispatchEvent('onSortChange', sort);
	}

	const isAllSelected = data.length !== 0 && data.length === selected.length;

	const dispatchEvent = (name, ...args) => {
		const fn = props[name];
		fn && fn(...args);
	}

	const classes = classNames('wp-list-table', 'widefat', 'fixed', 'striped', 'items', 'ea-list-table', className);
	console.log(selected);
	return (
		<table className={classes}>
			<thead>

			<TableHeader
				className='table-header'
				columns={columns}
				sort={sort}
				setSort={handleSort}
				isAllSelected={isAllSelected}
				isLoading={isLoading}
				onSelectAll={handleSelectAll}/>
			</thead>

			<tbody>

			<TableBody
				data={data}
				columns={columns}
				isLoading={isLoading}
				noResult={noResult}
				selected={selected}
				onSelect={handleSelection }/>
			</tbody>

			<tfoot>
			<TableHeader
				className='table-header'
				columns={columns}
				sort={sort}
				setSort={handleSort}
				isAllSelected={isAllSelected}
				isLoading={isLoading}
				onSelectAll={handleSelectAll}/>
			</tfoot>

		</table>
	);

}

Table.propTypes = {
	columns: PropTypes.arrayOf(PropTypes.shape({
		//type of the column. If set to selection, the column will display checkbox
		type: PropTypes.string,
		//column label
		label: PropTypes.string,
		//field name. You can also use its alias: property
		prop: PropTypes.string,
		//column width
		width: PropTypes.number,
		//with width has a fixed width, while columns with minWidth
		minWidth: PropTypes.number,
		//custom render Function
		render: PropTypes.func,
		//render function for table header of this column
		renderHeader: PropTypes.func,
		//is sortable or not
		sortable: PropTypes.bool,
		//alignment
		align: PropTypes.oneOf(['left', 'center', 'right']),
		//header alignment
		headerAlign: PropTypes.oneOf(['left', 'center', 'right']),
		//classname
		className: PropTypes.string,

	})),
	//table data
	data: PropTypes.arrayOf(PropTypes.object),
	//weather to show header or not
	showHeader: PropTypes.bool,
	//default sort
	defaultSort: PropTypes.shape({orderby: PropTypes.string, order: PropTypes.oneOf(['asc', 'desc'])}),
	//on sort change
	onSortChange: PropTypes.func,
	//on select item
	onSelect: PropTypes.func,
	//on all select
	onSelectAll: PropTypes.func,
	//on select change
	onSelectChange: PropTypes.func,
	//is loading
	isLoading: PropTypes.func,
	//no result
	noResult: PropTypes.oneOfType(PropTypes.string, PropTypes.node)
};

export default Table;
