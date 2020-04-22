import { Component, Fragment } from 'react';
import { __ } from '@wordpress/i18n';
import { Link } from 'react-router-dom';
import { withListTable } from '@eaccounting/hoc';
import { SearchBox, TableNav, Table, EmptyContent } from '@eaccounting/components';
import { getHeaders, getBulk } from './constants';
import { Dashicon } from '@wordpress/components';
import Row from './row';

class Transfers extends Component {
	constructor(props) {
		super(props);
		this.renderRow = this.renderRow.bind(this);
		this.renderTable = this.renderTable.bind(this);
		this.emptyTable = this.emptyTable.bind(this);
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
						{__('Add Transfer')}
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

	emptyTable() {
		const { match } = this.props;
		return (
			<EmptyContent icon={'info'} title={'Transfers'}>
				<div className="ea-empty-content__subtitle">
					<p>
						Transfers allow you to move money from one account to another, whether they use the same currency or not.
						Check out the documentation for more details.
					</p>
				</div>
				<Link to={`${match.path}/add`} className="ea-button button">
					<Dashicon icon="plus" />
					{__('Add Transfer')}
				</Link>
			</EmptyContent>
		);
	}

	render() {
		const { status, total, hasFilter } = this.props;
		return (
			<Fragment>
				{!hasFilter && status === 'STATUS_COMPLETE' && total === 0 ? this.emptyTable() : this.renderTable()}
			</Fragment>
		);
	}
}

export default withListTable({
	queryFilter: query => {
		return query;
	},
})(Transfers);
