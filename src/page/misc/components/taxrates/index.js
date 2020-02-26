import {Component, Fragment} from "react";
import {map} from 'lodash';

/**
 * Internal dependencies
 */
import {
	getItems,
	createItem,
	setPage,
	performTableAction,
	setAllSelected,
	setOrderBy,
	setSearch,
	setFilter,
	setDisplay
} from 'state/taxrates/action';
import {translate as __} from 'lib/locale';
import {getBulk, getHeaders} from "./constants";
import Table from 'component/table';
import TableNav from 'component/table/navigation';
import SearchBox from 'component/search-box';
import BulkAction from 'component/table/bulk-action';
import {STATUS_COMPLETE, STATUS_IN_PROGRESS, STATUS_SAVING} from 'lib/status';
import EditTaxRate from 'component/edit-taxrate';
import {connect} from "react-redux";
import {Button} from "@wordpress/components";
import Row from "./row";
import {SelectControl} from "@eaccounting/components";
const taxTypes = [
	{
		label: __('Normal'),
		value: 'normal',
	},
	{
		label: __('Inclusive'),
		value: 'inclusive',
	},
	{
		label: __('Compound'),
		value: 'compound',
	}
];

class TaxRates extends Component {
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
		const { saving } = this.props.taxrates;
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
		const {status, total, table, rows, saving} = this.props.taxrates;
		const {isAdding,} = this.state;
		const {type = []} = table.filterBy;
		const typeFilter = taxTypes.filter((filter, index) => {
			return type.includes(filter.value) === true;
		});

		return(
			<Fragment>
				{isAdding && <EditTaxRate onClose={this.onClose}/>}
				<div className="ea-table-display">
					<Button className="page-title-action" onClick={this.onAdd}>{__('Add Rate')}</Button>
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
	const {taxrates} = state;
	return {
		taxrates,
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
)(TaxRates);
