import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import { fetchCategories, BulkAction } from 'store/categories';
import { getHeaders, getBulk } from './constants';
import {Button, Navigation, SearchBox, Table} from '@eaccounting/components';
import Row from './row';
import EditCategory from "component/edit-category";
class Categories extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding:false
		};
	}

	componentDidCatch(error, info) {
		this.setState({ error: true, stack: error, info });
	}

	componentDidMount() {
		this.props.onMount({});
	}

	onAdd = ev => {
		ev.preventDefault();
		this.setState({ isAdding: !this.state.isAdding });
	};

	onCreate = () => {
		this.props.onMount({});
	};

	onClose = () => {
		this.setState({ isAdding: !this.state.isAdding });
	};


	onRenderRow = (item, pos, status, search) => {
		const { selected } = this.props.table;
		return (
			<Row
				item={item}
				key={pos}
				disabled={status.isLoading}
				search={search}
				isSelected={selected.includes(item.id)}
				{...this.props}
			/>
		);
	};

	render() {
		const { status, total, table, rows, match } = this.props;
		return (
			<Fragment>
				<Button className="page-title-action" onClick={this.onAdd}>
					{__('Add Category')}
				</Button>

				{this.state.isAdding && <EditCategory onClose={this.onClose} onCreate={this.onCreate}/>}
				<div className="ea-table-display">
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

				<Navigation
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					status={status}
				/>
			</Fragment>
		);
	}
}

const mapStateToProps = state => {
	return state.categories;
};

function mapDispatchToProps(dispatch) {
	return {
		onMount: params => {
			dispatch(fetchCategories(params));
		},
		onSetOrderBy: (orderby, order) => {
			dispatch(fetchCategories({ orderby, order }));
		},
		onChangePage: page => {
			dispatch(fetchCategories({ page }));
		},
		onSearch: search => {
			dispatch(fetchCategories({ search }));
		},
		onSetAllSelected: onoff => {
			dispatch({ type: 'CATEGORIES_ALL_SELECTED', payload: onoff });
		},
		onAction: action => {
			dispatch(BulkAction(action));
		},
	};
}

export default connect(mapStateToProps, mapDispatchToProps)(Categories);
