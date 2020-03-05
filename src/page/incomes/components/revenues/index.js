import { Component, Fragment } from 'react';
import { connect } from 'react-redux';
import { translate as __ } from 'lib/locale';
import { STATUS_IN_PROGRESS, STATUS_COMPLETE, STATUS_FAILED, STATUS_SAVING } from 'status';
import {
	setGetItems,
	setPage,
	setBulkAction,
	setAllSelected,
	setOrderBy,
	setSearch,
	setFilter,
} from 'state/revenues/action';
import { getHeaders, getBulk } from './constants';
import { SelectControl, Table, Navigation, SearchBox, BulkAction, Button } from '@eaccounting/components';
import Row from './row';
import { Link } from 'react-router-dom';

class Revenues extends Component {
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
		this.setState({ isAdding: !this.state.isAdding });
	};

	onClose = () => {
		this.setState({ isAdding: !this.state.isAdding });
	};

	onRenderRow = (item, pos, status, search) => {
		const { saving } = this.props.revenues;
		const loadingStatus = status.isLoading ? STATUS_IN_PROGRESS : STATUS_COMPLETE;
		const rowStatus = saving.indexOf(item.id) !== -1 ? STATUS_SAVING : loadingStatus;
		return <Row item={item} key={pos} status={rowStatus} search={search} {...this.props} />;
	};

	render() {
		const { status, total, table, rows, saving } = this.props.revenues;
		const { isAdding } = this.state;
		const { match } = this.props;
		return (
			<Fragment>
				{/*{isAdding && <EditAccount onClose={this.onClose}/>}*/}

				<div className="ea-table-display">
					<Link className="page-title-action" to={`${match.path}/new`}>
						{__('Add Revenue')}
					</Link>
					<SearchBox status={status} table={table} onSearch={this.props.onSearch} />
				</div>

				<Navigation
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					status={status}
					bulk={getBulk()}
				/>

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
		);
	}
}

function mapStateToProps(state) {
	const { revenues } = state;
	return {
		revenues,
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
		onAction: action => {
			dispatch(setBulkAction(action));
		},
		onSetAllSelected: onoff => {
			dispatch(setAllSelected(onoff));
		},
		onSetOrderBy: (column, order) => {
			dispatch(setOrderBy(column, order));
		},
		onFilter: filterBy => {
			dispatch(setFilter(filterBy));
		},
		onSearch: search => {
			dispatch(setSearch(search));
		},
	};
}

export default connect(mapStateToProps, mapDispatchToProps)(Revenues);
