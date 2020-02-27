import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';


/**
 * Internal dependencies
 */
import {
	getItems,
	setPage,
	performTableAction,
	setAllSelected,
	setOrderBy,
	setSearch,
	setFilter,
	setDisplay
} from 'state/currencies/action';

import {getBulk, getHeaders} from "./constants";
import Table from 'component/table';
import TableNav from 'component/table/navigation';
import SearchBox from 'component/search-box';
import BulkAction from 'component/table/bulk-action';
import {STATUS_COMPLETE, STATUS_IN_PROGRESS, STATUS_SAVING} from 'lib/status';
import EditCurrency from 'component/edit-currency';
import {connect} from "react-redux";
import {Button} from "@wordpress/components";
import Row from "./row";
import {SelectControl} from "@eaccounting/components";

class Currencies extends Component {
	constructor( props ) {
		super(props);
		this.state = {
			isAdding:false,
		};
	}

	componentDidCatch( error, info ) {
		this.setState( { error: true, stack: error, info } );
	}

	componentDidMount() {
		this.props.onLoadItems({});
	}


	onAdd = ev =>{
		ev.preventDefault();
		this.setState({isAdding:!this.state.isAdding});
	};

	onClose = () =>{
		this.setState({isAdding:!this.state.isAdding});
	};

	setFilter = (filter, value) => {
		const {filterBy} = this.props.taxrates.table;
		this.props.onFilter({...filterBy, [filter]: value ? value : undefined});
	};

	onFilterType = (types) => {
		this.setFilter('type', map(types, 'value'));
	};

	onRenderRow = (item, pos, status, search) => {
		const { saving } = this.props.currencies;
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
		const {status, total, table, rows, saving} = this.props.currencies;
		const {isAdding,} = this.state;
		return(
			<Fragment>
				{isAdding && <EditCurrency onClose={this.onClose}/>}
				<div className="ea-table-display">
					<Button className="page-title-action" onClick={this.onAdd}>{__('Add Currency')}</Button>
					<SearchBox
						status={ status }
						table={ table }
						onSearch={ this.props.onSearch }
					/>
				</div>

				<TableNav total={total} selected={table.selected} table={table} onChangePage={this.props.onChangePage}
						  onAction={this.props.onAction} status={status} bulk={getBulk()}>
					<BulkAction/>

					<SelectControl
						className={'alignleft actions'}
						placeholder={__('Select Type')}
						options={taxTypes}
						isMulti
						value={typeFilter}
						onChange={this.onFilterType}
					/>

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
	const {currencies} = state;
	return {
		currencies,
	};
}

function mapDispatchToProps(dispatch) {
	return {
		onLoadItems: () => {
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
)(Currencies);
