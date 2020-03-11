import PropTypes from 'prop-types';

const isDisabledHeader = (isLoading, rows) => isLoading || rows.length === 0;
const isSelectedHeader = (selected, rows) => selected.length === rows.length && rows.length !== 0;

const ListTable = props => {
	const {headers, row, rows, total, selected, orderby, order, isLoading,status, onSetAllSelected, onSetOrderBy} = props;
	const isDisabled = isDisabledHeader(status, rows);
	const isSelected = isSelectedHeader(selected, rows);
	let content = null;

	if (isLoading && rows.length === 0) {
		content = <LoadingRow headers={headers} rows={rows} />;
	} else if (rows.length === 0 && !isLoading) {
		content = <EmptyRow headers={headers} />;
	} else if (status === 'STATUS_FAILED') {
		content = <FailedRow headers={headers} />;
	} else if (rows.length > 0) {
		content = <DataRow rows={rows} isLoading={isLoading} selected={selected} row={row} />;
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

ListTable.propTypes = {
	headers: PropTypes.array.isRequired,
	row: PropTypes.func.isRequired,
	rows: PropTypes.array.isRequired,
	selected: PropTypes.array.isRequired,
	onSetAllSelected: PropTypes.func,
	onSetOrderBy: PropTypes.func,
	isLoading: PropTypes.bool.isRequired,
	total: PropTypes.number.isRequired,
	orderby: PropTypes.string.isRequired,
	order: PropTypes.string.isRequired,
};

export default ListTable;
