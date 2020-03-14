/**
 * External dependencies
 */

import React from 'react';
import classnames from 'classnames';
import PropTypes from 'prop-types';

const SortableColumn = props => {
	const { name, text, primary, order, orderby } = props;
	const click = ev => {
		ev.preventDefault();
		props.onSetOrderBy(name, orderby === name && order === 'desc' ? 'asc' : 'desc');
	};
	const classes = classnames({
		'manage-column': true,
		sortable: true,
		asc: orderby === name && order === 'asc',
		desc: (orderby === name && order === 'desc') || orderby !== name,
		'column-primary': primary,
		['column-' + name]: true,
	});

	return (
		<th scope="col" className={classes} onClick={click}>
			<a href="#">
				<span>{text}</span>
				<span className="sorting-indicator"/>
			</a>
		</th>
	);
};

SortableColumn.propTypes = {
	orderby: PropTypes.string.isRequired,
	order: PropTypes.string.isRequired,
	name: PropTypes.string.isRequired,
	text: PropTypes.string.isRequired,
	onSetOrderBy: PropTypes.func.isRequired,
};

export default SortableColumn;
