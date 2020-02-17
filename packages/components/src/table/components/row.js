import PropTypes from 'prop-types';
import React, {Component, Fragment} from 'react';

class Row extends Component {
	static propTypes = {
		columns: PropTypes.array.isRequired,
		row: PropTypes.object,
		onRenderCell: PropTypes.func
	};

	render() {
		const {row, columns, onRenderCell} = this.props;
		let cell;

		return (
			<Fragment>
				{columns.map((column, index) => {
					cell = row[column.id] || '—';

					if (typeof onRenderCell !== 'undefined') {
						cell = onRenderCell(column, row);
					}

					return (
						<td key={index} className={`${column.id} column-${column.id}`} data-colname={column.id}>
							{cell}
						</td>
					)
				})}
			</Fragment>
		)
	}
}

export default Row;
