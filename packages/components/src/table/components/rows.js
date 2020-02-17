import PropTypes from 'prop-types';
import React, {Component, Fragment} from 'react';
import Row from './row';
import {range} from 'lodash';

const Placeholder = props => {
	const { columns } = props;
	return (
		<tr className="is-placeholder">
			{columns.map((item, pos) => (
				<td key={pos}>
					<div className="placeholder-loading"/>
				</td>
			))}
		</tr>
	);
};

class Rows extends Component {
	static propTypes = {
		columns: PropTypes.array.isRequired,
		rows: PropTypes.array.isRequired,
		status: PropTypes.string,
	};

	render() {
		const {rows, columns, status} = this.props;
		const ready = status === 'ready';
		return (
			<Fragment>

				{!ready && range(5).map((row, index) => {
					return (<Placeholder key={index} columns={columns}/>)
				})}

				{ready && rows.map((row, index) => {
					return (
						<tr key={index}>
							<Row key={index} row={row} columns={columns}/>
						</tr>
					)
				})}
			</Fragment>
		)
	}
}

export default Rows;
