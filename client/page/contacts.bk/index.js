import {Component, Fragment} from 'react';
import withContacts from "hocs/with-contacts";
import {SearchBox, TableNav, Table,SelectControl, AccountControl,CategoryControl, DateFilter} from "@eaccounting/components"
import {getHeaders, getBulk} from './constants';
import Row from "./row";
import {getOptions} from "options";
import {__} from '@wordpress/i18n';
import {map} from "lodash"

class Contacts extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	onRenderRow = (item, pos, isSelected, isLoading, search) => {
		return(
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
		const {status, total, items, query, selected} = this.props;
		const {page = 1, orderby = 'paid_at', order = 'desc'} = query;

		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Contacts')}</h1>
				<hr className="wp-header-end" />

				<div className="ea-table-display">
					<SearchBox status={status} onSearch={this.props.onSearch}/>
				</div>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					bulk={getBulk()}
				>
					<SelectControl
						className={'alignleft actions'}
						placeholder={__('Filter Type')}
						options={[
							{
								label: __('Customer'),
								value: 'customer'
							},
							{
								label: __('Vendor'),
								value: 'vendor'
							}
						]}
						isMulti
						onChange={(types) => {this.props.onFilter({types: map(types, 'value')})}}
					/>
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
					onSetAllSelected={this.props.onSetAllSelected}
					onSetOrderBy={this.props.onSetOrderBy}
				/>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
				/>

			</Fragment>
		);
	}
}

export default withContacts(Contacts);
