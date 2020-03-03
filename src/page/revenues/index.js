import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';
import {loadRevenues} from "store/revenues";
import {getHeaders} from "./constants";
import {Navigation, SearchBox, Table} from "@eaccounting/components";
import {STATUS_IN_PROGRESS, STATUS_COMPLETE} from 'lib/status';
import Row from './row';
import {Link} from "component/link";
import {getBulk} from "../incomes/components/revenues/constants";

class Revenues extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidMount() {
		this.props.onMount({});
	}

	onRenderRow = (item, pos, status, search) => {
		const {saving} = this.props;
		const loadingStatus = status.isLoading ? STATUS_IN_PROGRESS : STATUS_COMPLETE;
		const rowStatus = saving.indexOf(item.id) !== -1 ? STATUS_SAVING : loadingStatus;
		return (
			<Row
				item={item}
				key={pos}
				status={rowStatus}
				search={search}
				{...this.props}/>
		);
	};

	goTo = (ev, route) => {
		ev.preventDefault();
		this.props.history.push(route);
	};

	render() {
		console.log(this.props);
		const {status, total, table, rows, saving} = this.props;
		return (
			<Fragment>
				<div className="ea-table-display">
					<a className="page-title-action" href="#" onClick={(e)=> this.goTo( e, `/new`)}>{__('Add Revenue')}</a>
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
					bulk={getBulk()}/>

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
					status={status}
					bulk={getBulk()}/>


			</Fragment>
		)
	}
}

const mapStateToProps = (state) => {
	return state.revenues
};

function mapDispatchToProps(dispatch) {
	return {
		onMount: (params) => {
			dispatch(loadRevenues(params));
		},
		onSetOrderBy: (order_by, order) => {
			dispatch(loadRevenues({order_by, order}));
		},
		onChangePage: (page) => {
			dispatch(loadRevenues({page}));
		},
		onSearch: (search) => {
			dispatch(loadRevenues({search}));
		},
		onAction: (search) => {
			dispatch(loadRevenues({search}));
		}
	}
}

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(Revenues);
