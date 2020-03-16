/**
 * External dependencies
 */

import React from 'react';
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import TableHeader from './header';
import DataRow from './row/data-row';
import LoadingRow from './row/loading-row';
import EmptyRow from './row/empty-row';
import FailedRow from './row/failed-row';

const isDisabledHeader = (status, rows) => status !== 'STATUS_COMPLETE' || rows.length === 0;
const isSelectedHeader = (selected, rows) => selected.length === rows.length && rows.length !== 0;

const Table = props => {
	const {headers, row, rows, total, selected, orderby, order, status, onSetAllSelected, onSetOrderBy, per_page=20} = props;
	const isDisabled = isDisabledHeader(status, rows);
	const isSelected = isSelectedHeader(selected, rows);

	let content = null;
	if (status === 'STATUS_IN_PROGRESS') {
		content = <LoadingRow headers={headers} rows={rows} per_page={per_page}/>;
	} else if (rows.length === 0 && status === 'STATUS_COMPLETE') {
		content = <EmptyRow headers={headers}/>;
	} else if (status === 'STATUS_FAILED') {
		content = <FailedRow headers={headers}/>;
	} else if (rows.length > 0) {
		content = <DataRow rows={rows} status={status} selected={selected} row={row}/>;
	}

	return (
		<table className="wp-list-table widefat fixed striped items">
			<thead>
			<TableHeader
				orderby={orderby}
				order={order}
				isDisabled={isDisabled}
				isSelected={isSelected}
				headers={headers}
				rows={rows}
				total={total}
				onSetOrderBy={onSetOrderBy}
				onSetAllSelected={onSetAllSelected}
			/>
			</thead>

			{content}

			<tfoot>
			<TableHeader
				orderby={orderby}
				order={order}
				isDisabled={isDisabled}
				isSelected={isSelected}
				headers={headers}
				rows={rows}
				total={total}
				onSetOrderBy={onSetOrderBy}
				onSetAllSelected={onSetAllSelected}
			/>
			</tfoot>
		</table>
	);
};

Table.propTypes = {
	headers: PropTypes.array.isRequired,
	row: PropTypes.func.isRequired,
	rows: PropTypes.array.isRequired,
	selected: PropTypes.array.isRequired,
	orderby: PropTypes.string.isRequired,
	order: PropTypes.string.isRequired,
	onSetAllSelected: PropTypes.func,
	onSetOrderBy: PropTypes.func,
	status: PropTypes.string.isRequired,
	total: PropTypes.number.isRequired,
};

Table.defaultProps = {
	order: 'desc',
	total: 0,
	onSetAllSelected: () => {
	},
	onSetOrderBy: () => {
	}
}

export default Table;
