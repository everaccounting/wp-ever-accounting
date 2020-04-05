import {Component, Fragment} from 'react';
import {
	SearchBox,
	TableNav,
	Table,
	Button
} from "@eaccounting/components"
import {withTable} from "@eaccounting/hoc";
import {getHeaders, getBulk} from './constants';
import Row from "./row";
import {__} from '@wordpress/i18n';
import EditCurrency from "./edit-currency";

class Currencies extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding: false
		};

		this.openModal = this.openModal.bind(this);
		this.closeModal = this.closeModal.bind(this);
		this.onRenderRow = this.onRenderRow.bind(this);
	}

	openModal() {
		this.setState({isAdding: true});
	};

	closeModal() {
		this.setState({isAdding: false});
	};

	onRenderRow(item, pos, isSelected, isLoading, search) {
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

	render() {
		const {status, total, items, page, order, orderby, query, selected} = this.props;

		return (
			<Fragment>

				{this.state.isAdding && <EditCurrency
					onSubmit={(data) => this.props.handleSubmit(data, this.closeModal)}
					onClose={this.closeModal}
					tittle={__('Add Currency')}
					buttonTittle={__('Submit')}/>}

				<div className="ea-table-display">
					<Button className="page-title-action" onClick={this.openModal}>{__('Add Currency')}</Button>
					<SearchBox status={status} onSearch={this.props.onSearch}/>
				</div>

				<TableNav
					status={status}
					total={total}
					page={page}
					onChangePage={this.props.onPageChange}/>

				<Table
					headers={getHeaders()}
					orderby={orderby}
					order={order}
					rows={items}
					total={total}
					row={this.onRenderRow}
					status={status}
					onSetOrderBy={this.props.onOrderBy}/>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.onPageChange}/>

			</Fragment>
		)
	}
}

export default withTable('currencies', {orderby: 'created_at'})(Currencies);
