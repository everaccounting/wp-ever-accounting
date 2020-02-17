import React, {Component} from 'react';
import classnames from 'classnames';
import propTypes from 'prop-types';
import Column from './column';

class Columns extends Component {
	static propTypes = {
		columns: propTypes.array.isRequired,
		orderby: propTypes.string,
		order: propTypes.string,
		onSort: propTypes.func,
	};


	render() {
		const {columns, orderby, order, onSort} = this.props;
		return(
			<tr>
				{columns.map((column, index) =>{
					return(
						<Column key={index} column={column} orderby={orderby} order={order} onSort={onSort}/>
					)
				})}
			</tr>
		)
	}
}
export default Columns;
