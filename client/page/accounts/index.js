import {Component, Fragment} from 'react';
import {
	SearchBox,
	TableNav,
	Table,
	Button
} from "@eaccounting/components"
import {withListTable} from "@eaccounting/hoc";
import {getHeaders, getBulk} from './constants';
import Row from "./row";
import {__} from '@wordpress/i18n';
import EditAccount from "./edit-account";

class Accounts extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding: false
		};

		this.openModal = this.openModal.bind(this);
		this.closeModal = this.closeModal.bind(this);
		this.renderRow = this.renderRow.bind(this);
	}

	openModal() {
		this.setState({isAdding: true});
	};

	closeModal() {
		this.setState({isAdding: false});
	};

	renderRow(item, pos, isSelected, isLoading, search) {
		console.log(isSelected);
		return (
			<Row
				item={item}
				key={pos}
				isLoading={isLoading}
				search={search}
				isSelected={isSelected}
				{...this.props}
			/>
		)
	};

	getTableFilters() {
		return "HELLO";
	}

	render() {
		console.log(this.props);

		const {status, total, items, page, order, orderby = 'created_at', query, selected} = this.props;
		return (
			<Fragment>
				{/*{this.state.isAdding &&*/}
				{/*<EditAccount*/}
				{/*	onSubmit={(data) => this.props.handleSubmit(data, this.closeModal)}*/}
				{/*	onClose={this.closeModal}*/}
				{/*	tittle={__('Add Account')}*/}
				{/*	buttonTittle={__('Submit')}/>}*/}

				<div className="ea-table-display">
					<button className="page-title-action" onClick={this.openModal}>{__('Add Account')}</button>
					<SearchBox status={status} onSearch={this.props.setSearch}/>
				</div>

				<TableNav
					status={status}
					total={total}
					page={page}
					onChangePage={this.props.setPageChange}/>

				<Table
					headers={getHeaders()}
					orderby={orderby}
					order={order}
					rows={items}
					total={total}
					selected={selected}
					onSetAllSelected={this.props.setAllSelected}
					onSetSelected={this.props.setSelected}
					row={this.renderRow}
					status={status}
					onSetOrderBy={this.props.setOrderBy}/>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.setPageChange}/>

			</Fragment>
		);
	}
}

export default withListTable({
	bulks: getBulk(),
	headers: getHeaders(),
})(Accounts)
