import {Component, Fragment} from 'react';
import {
	SearchBox,
	TableNav,
	Table,
	SelectControl,
	AccountControl,
	CategoryControl,
	DateFilter,
	withTable,
} from "@eaccounting/components"
import {data} from "@eaccounting/data";

import {getHeaders} from './constants';
import Row from "./row";
import {__} from '@wordpress/i18n';
import {map} from "lodash"

class Transactions extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	onRenderRow = (item, pos, isSelected, isLoading, search) => {
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
		console.log(data);
		const {status, total, items, query, selected} = this.props;
		const {page = 1, orderby = 'paid_at', order = 'desc'} = query;
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Transactions')}</h1>
				<hr className="wp-header-end"/>

				<div className="ea-table-display">
					<SearchBox status={status} onSearch={this.props.onSearch}/>
				</div>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.onPageChange}>

				{/*	<DateFilter*/}
				{/*		className={'alignleft actions'}*/}
				{/*		onChange={date => this.props.onFilter({date})}/>*/}

					<AccountControl
						className={'alignleft actions'}
						placeholder={__('Filter Account')}
						isMulti
						onChange={(accounts) => this.props.onFilter({account_id: map(accounts, 'id')})}
					/>

					<CategoryControl
						className={'alignleft actions'}
						placeholder={__('Filter Category')}
						isMulti
						type={['income', 'expense']}
						onChange={(categories) => this.props.onFilter({category_id: map(categories, 'id')})}
					/>

				{/*	<SelectControl*/}
				{/*		className={'alignleft actions'}*/}
				{/*		placeholder={__('Filter Type')}*/}
				{/*		options={[*/}
				{/*			{*/}
				{/*				label: __('Income'),*/}
				{/*				value: 'income'*/}
				{/*			},*/}
				{/*			{*/}
				{/*				label: __('Expense'),*/}
				{/*				value: 'expense'*/}
				{/*			},*/}
				{/*			{*/}
				{/*				label: __('Transfer'),*/}
				{/*				value: 'transfer'*/}
				{/*			}*/}
				{/*		]}*/}
				{/*		isMulti*/}
				{/*		onChange={(types) => this.props.onFilter({type: map(types, 'value')})}/>*/}

				</TableNav>

				<Table
					headers={getHeaders()}
					orderby={orderby}
					selected={selected}
					order={order}
					rows={items}
					total={total}
					row={this.onRenderRow}
					status={status}
					onSetOrderBy={this.props.onOrderBy}
				/>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.onPageChange}
					onAction={this.props.onAction}
				/>

			</Fragment>
		);
	}
}

export default withTable('transactions', {orderby: 'paid_at'})(Transactions);
