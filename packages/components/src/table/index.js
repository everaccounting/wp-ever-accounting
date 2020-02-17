import PropTypes from 'prop-types';
import React, {Component} from 'react';
import Columns from './components/columns';
import Rows from './components/rows';

class Table extends Component {
	static propTypes = {
		columns: PropTypes.array.isRequired,
		rows: PropTypes.array.isRequired,
		onSort: PropTypes.func,
		order: PropTypes.string,
		orderby: PropTypes.string,
		status: PropTypes.string,
	};

	render() {
		const {
			columns,
			rows,
			status = '',
			onSort = () => {
			},
			order = 'asc',
			orderby = '',
		} = this.props;

		return (
			<table className="wp-list-table widefat fixed striped items ea-table">
				<thead>
				<Columns columns={columns} onSort={onSort} orderby={orderby} order={order} status={status}/>
				</thead>
				<tbody>
				<Rows rows={rows} columns={columns} status={status}/>
				</tbody>
				<tfoot>
				<Columns columns={columns} onSort={onSort} orderby={orderby} order={order}/>
				</tfoot>
			</table>
		)
	}
}


export default Table;
