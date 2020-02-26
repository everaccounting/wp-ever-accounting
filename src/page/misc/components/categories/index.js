import {Component, Fragment} from 'react';
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';
import {map} from 'lodash';

/**
 * Internal dependencies
 */
import {
	getCategories,
	createContact,
	setPage,
	performTableAction,
	setAllSelected,
	setOrderBy,
	setSearch,
	setFilter,
	setDisplay
} from 'state/categories/action';
import {Button} from "@wordpress/components";
import {getBulk, getHeaders} from "./constants";
import Table from 'component/table';
import TableNav from 'component/table/navigation';
import SearchBox from 'component/search-box';
import BulkAction from 'component/table/bulk-action';
import CategoriesRow from "./row";
import {STATUS_COMPLETE, STATUS_IN_PROGRESS, STATUS_SAVING} from 'lib/status';
import {initialCategory} from 'state/categories/selection';
import EditCategory from "component/edit-category";
import CategoryImporter from "./importer";
class Categories extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding:false,
			isImporting:false,
		};
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	componentDidMount() {
		this.props.onLoadCategories({});
	}

	onRenderRow = (item, pos, status, search) => {
		const { saving } = this.props.categories;
		const loadingStatus = status.isLoading ? STATUS_IN_PROGRESS : STATUS_COMPLETE;
		const rowStatus = saving.indexOf( item.id ) !== -1 ? STATUS_SAVING : loadingStatus;
		return (
			<CategoriesRow
				item={item}
				key={pos}
				status={rowStatus}
				search={search}
				selected={ status.isSelected }
			/>
		);
	};

	onAdd = ev =>{
		ev.preventDefault();
		this.setState({isAdding:!this.state.isAdding});
	};

	onClose = () =>{
		this.setState({isAdding:!this.state.isAdding});
	};

	toggleImport = (e) => {
		e.preventDefault();
		this.setState({isImporting:!this.state.isImporting});
	};

	render() {
		const {status, total, table, rows, saving} = this.props.categories;
		const {isAdding, isImporting} = this.state;
		return (
			<Fragment>

				<div className="ea-table-display">
					<Button className="page-title-action" onClick={this.onAdd}>{__('Add Category')}</Button>
					<Button className="page-title-action" onClick={this.toggleImport}>{__('Import')}</Button>
					<a href={`${eAccountingi10n.pluginRoot}&eaccounting-action=category-export`}
					   className="page-title-action" target='_blank'>{__('Export')}</a>
					{isAdding && <EditCategory item={initialCategory} onClose={this.onClose}/>}

					<SearchBox
						status={ status }
						table={ table }
						onSearch={ this.props.onSearch }
					/>
				</div>

				{isImporting && <CategoryImporter onClose={this.toggleImport}/>}

				<TableNav total={total} selected={table.selected} table={table} onChangePage={this.props.onChangePage}
						  onAction={this.props.onAction} status={status} bulk={getBulk()}>
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

				<TableNav
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					status={status}/>
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
		onLoadCategories: () => {
			dispatch(getCategories());
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
			dispatch(createContact(item));
		},
		onSetDisplay: (displayType, displaySelected) => {
			dispatch(setDisplay(displayType, displaySelected));
		},
	}
}

export default connect(
	mapStateToProps,
	mapDispatchToProps,
)(Categories);
