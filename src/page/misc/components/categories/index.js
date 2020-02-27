/**
 * External dependencies
 */
import {Component, Fragment} from "react";
import {connect} from "react-redux";

/**
 * Internal dependencies
 */
import {translate as __} from 'lib/locale';
import {
	getItems,
	createItem,
	setPage,
	performTableAction,
	setAllSelected,
	setOrderBy,
	setSearch,
	setFilter,
} from 'state/categories/action';
import Table from 'component/table';
import TableNav from 'component/table/navigation';
import SearchBox from 'component/search-box';
import BulkAction from 'component/table/bulk-action';
import Row from "./row";
import {getHeaders} from "./constants";
import { STATUS_IN_PROGRESS, STATUS_FAILED, STATUS_COMPLETE } from 'lib/status';
import {getBulk} from "../currencies/constants";

class Categories extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding: false,
		};
	}

	componentDidMount() {
		this.props.onMount();
	}

	onRenderRow = (item, pos, status, search) => {
		const { saving } = this.props.categories;
		const loadingStatus = status.isLoading ? STATUS_IN_PROGRESS : STATUS_COMPLETE;
		const rowStatus = saving.indexOf( item.id ) !== -1 ? STATUS_SAVING : loadingStatus;
		return (
			<Row
				item={item}
				key={pos}
				status={rowStatus}
				search={search}
				selected={ status.isSelected }
			/>
		);
	};


	render() {
		const {status, total, table, rows, saving} = this.props.categories;
		const {isAdding,} = this.state;

		return(
			<Fragment>
				<TableNav
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					status={status}
					bulk={getBulk()}>

					<BulkAction/>
				</TableNav>

				<Table
					headers={getHeaders()}
					rows={rows}
					total={total}
					row={this.onRenderRow}
					table={table}
					status={status}
					onSetAllSelected={this.props.onSetAllSelected}
					onSetOrderBy={this.props.onSetOrderBy}
				/>
			</Fragment>
		)
	}

}


function mapStateToProps(state) {
	const {categories} = state;
	return {
		categories,
	};
}

function mapDispatchToProps(dispatch) {
	return {
		onMount: () => {
			dispatch(getItems());
		},
		onChangePage: page => {
			dispatch(setPage(page));
		},
		onAction: action => {
			dispatch(performTableAction(action));
		},
		onSetAllSelected: onoff => {
			dispatch(setAllSelected(onoff));
		},
		onSetOrderBy: (column, order) => {
			dispatch(setOrderBy(column, order));
		},
		onFilter: (filterBy) => {
			dispatch(setFilter(filterBy));
		},
		onSearch: (search) => {
			dispatch(setSearch(search));
		},
		onCreate: item => {
			//dispatch(createItem(item));
		},
	}
}
export default connect(
	mapStateToProps,
	mapDispatchToProps,
)(Categories);
