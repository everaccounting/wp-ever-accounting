import React, {Component} from 'react';
import classnames from 'classnames';
import propTypes from 'prop-types';
import Columns from "./columns";


class Column extends Component {
	static propTypes = {
		column: propTypes.object,
		orderby: propTypes.string,
		order: propTypes.string,
		onSort: propTypes.func,
	};


	render() {
		const {column, orderby, order, onSort} = this.props;
		const {id, name, sortable = false} = column;
		const classes = classnames({
			'manage-column': true,
			sortable: sortable,
			asc: orderby === id && order === 'asc',
			desc: (orderby === id && order === 'desc') || orderby !== id,
			['column-' + id]: true,
		});

		const click = ev => {
			ev.preventDefault();
			this.props.onSort(id, orderby === id && order === 'desc' ? 'asc' : 'desc');
		};

		return (

			<th scope="col" className={classes}>
				{sortable && <a href="#" onClick={click}><span>{name}</span><span className="sorting-indicator"/></a>}
				{!sortable && <>{name}</>}
			</th>
		);
	}
}

export default Column;
