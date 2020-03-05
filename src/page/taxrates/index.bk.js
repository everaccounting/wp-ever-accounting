/**
 * External dependencies
 */
import {Component, Fragment} from "react";
import {connect} from "react-redux";
import {map} from 'lodash';

/**
 * Internal dependencies
 */
import {translate as __} from 'lib/locale';
import {getSelectedOptions} from "lib/table";
import EditTaxRate from "component/edit-taxrate";
import {STATUS_IN_PROGRESS, STATUS_SAVING, STATUS_COMPLETE} from 'status';
import {
	setGetItems,
	setPage,
	setBulkAction,
	setAllSelected,
	setOrderBy,
	setSearch,
	setFilter,
} from 'state/taxrates/action';
import {SelectControl, Table, Navigation, SearchBox, BulkAction, Button} from "@eaccounting/components";
import Row from "./row";
import {getHeaders, getBulk} from "./constants";
import {taxTypes} from "state/taxrates/initial";
import './style.scss';

class TaxRates extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding: false,
		};
	}

	componentDidMount() {
		this.props.onMount();
	}

	onAdd = ev => {
		ev.preventDefault();
		this.setState({isAdding: !this.state.isAdding});
	};

	onClose = () => {
		this.setState({isAdding: !this.state.isAdding});
	};

	setFilter = (filter, value) => {
		const {filterBy} = this.props.taxrates.table;
		this.props.onFilter({...filterBy, [filter]: value ? value : undefined});
	};

	onFilterType = (types) => {
		this.setFilter('type', map(types, 'value'));
	};

	onRenderRow = (item, pos, status, search) => {
		const {saving} = this.props.taxrates;
		const loadingStatus = status.isLoading ? STATUS_IN_PROGRESS : STATUS_COMPLETE;
		const rowStatus = saving.indexOf(item.id) !== -1 ? STATUS_SAVING : loadingStatus;
		return (
			<Row
				item={item}
				key={pos}
				status={rowStatus}
				search={search}
				selected={status.isSelected}
			/>
		);
	};


	render() {
		const {status, total, table, rows, saving} = this.props.taxrates;
		const {isAdding,} = this.state;
		const {type=[]} = table.filterBy;
		return (
			<Fragment>
				{isAdding && <EditTaxRate onClose={this.onClose}/>}
				<div className="ea-table-display">
					<Button className="page-title-action" onClick={this.onAdd}>{__('Add Tax Rate')}</Button>
					<SearchBox
						status={status}
						table={table}
						onSearch={this.props.onSearch}
					/>
				</div>

				<Navigation
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					status={status}
					bulk={getBulk()}>

					<BulkAction/>

					<SelectControl
						className={'alignleft actions'}
						placeholder={__('Filter Type')}
						options={taxTypes}
						isMulti
						isDisabled={status !== STATUS_COMPLETE}
						value={getSelectedOptions(taxTypes, type)}
						onChange={this.onFilterType}
					/>

				</Navigation>

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

				<Navigation
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
		onMount: () => {
			dispatch(setGetItems());
		},
		onChangePage: page => {
			dispatch(setPage(page));
		},
		onAction: (action) => {
			dispatch(setBulkAction(action));
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
		}
	}
}

export default connect(
	mapStateToProps,
	mapDispatchToProps,
)(TaxRates);
