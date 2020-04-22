import { Component, Fragment } from 'react';
import { __ } from '@wordpress/i18n';
import { Link } from 'react-router-dom';
import { withListTable } from '@eaccounting/hoc';
import {
	SearchBox,
	TableNav,
	Table,
	SelectControl,
	AccountSelect,
	CategorySelect,
	DateFilter,
} from '@eaccounting/components';
import { map } from 'lodash';
import { getHeaders } from './constants';
import Row from './row';

class Transactions extends Component {
	constructor(props) {
		super(props);
		this.renderRow = this.renderRow.bind(this);
		this.renderTable = this.renderTable.bind(this);
	}

	renderRow(item, pos, isSelected, isLoading, search) {
		return <Row item={item} key={pos} isLoading={isLoading} search={search} isSelected={isSelected} {...this.props} />;
	}

	renderTable() {
		const { status, total, page, match, orderby, order, items, selected } = this.props;

		return (
			<Fragment>
				<div className="ea-table-display">
					<h1 className="wp-heading-inline">{__('Transactions')}</h1>
					<Link className="page-title-action" to={`/sales/revenues/add`}>
						{__('Add Revenue')}
					</Link>
					<Link className="page-title-action" to={`/sales/purchases/add`}>
						{__('Add Payment')}
					</Link>

					<SearchBox status={status} onSearch={this.props.setSearch} />
				</div>

				<TableNav status={status} total={total} page={page} selected={selected} onChangePage={this.props.setPage}>
					<DateFilter className={'alignleft actions'} onChange={date => this.props.setFilter({ date })} />
					<AccountSelect
						className={'alignleft actions'}
						placeholder={__('Filter Account')}
						isMulti
						onChange={accounts => this.props.setFilter({ account_id: map(accounts, 'id') })}
					/>

					<CategorySelect
						className={'alignleft actions'}
						placeholder={__('Filter Category')}
						isMulti
						type={['income', 'expense']}
						onChange={categories => this.props.setFilter({ category_id: map(categories, 'id') })}
					/>

					<SelectControl
						className={'alignleft actions'}
						placeholder={__('Filter Type')}
						options={['All', 'income', 'expense'].map(key => ({ label: key, value: key }))}
						clearable
						onChange={type => this.props.setFilter({ type })}
					/>
				</TableNav>

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
					onSetOrderBy={this.props.setOrderBy}
				/>

				<TableNav status={status} total={total} page={page} selected={selected} onChangePage={this.props.setPage} />
			</Fragment>
		);
	}

	render() {
		const { status, total } = this.props;
		return <Fragment>{this.renderTable()}</Fragment>;
	}
}

export default withListTable({
	queryFilter: query => {
		if (query.order && query.order === 'desc') {
			delete query.order;
		}
		query.include_transfer = true ;
		return query;
	},
})(Transactions);
