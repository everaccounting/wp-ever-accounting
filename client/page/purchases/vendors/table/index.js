import { Component, Fragment } from 'react';
import { __ } from '@wordpress/i18n';
import { Link } from 'react-router-dom';
import { withListTable } from '@eaccounting/hoc';
import { SearchBox, TableNav, Table } from '@eaccounting/components';
import { getHeaders, getBulk } from './constants';
import Row from './row';

class Vendors extends Component {
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
					<Link className="page-title-action" to={`${match.path}/add`}>
						{__('Add Vendor')}
					</Link>
					{/*<a className="page-title-action" href="/">*/}
					{/*	{__('Export')}*/}
					{/*</a>*/}
					{/*<a className="page-title-action" href="/">*/}
					{/*	{__('Import')}*/}
					{/*</a>*/}
					<SearchBox status={status} onSearch={this.props.setSearch} />
				</div>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					bulk={getBulk()}
					onChangePage={this.props.setPage}
					onAction={this.props.setAction}
				/>

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

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					bulk={getBulk()}
					onChangePage={this.props.setPage}
					onAction={this.props.setAction}
				/>
			</Fragment>
		);
	}

	render() {
		const { status, total } = this.props;
		return <Fragment>{this.renderTable()}</Fragment>;
	}
}

export default withListTable({
	resourceName: 'contacts',
	queryFilter: query => {
		query.type = 'vendor';
		return query;
	},
})(Vendors);
