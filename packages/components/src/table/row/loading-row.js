/**
 * External dependencies
 */

import React from 'react';
import PropTypes from 'prop-types';

const Row = props => {
	const { columns } = props;

	return (
		<tr className="is-placeholder">
			{columns.map((item, pos) => (
				<td key={pos}>
					<div className="placeholder-loading" />
				</td>
			))}
		</tr>
	);
};

const LoadingRow = props => {
	const { headers, per_page } = props;
	return (
		<tbody>
			<Row columns={headers} />

			{[...Array(per_page).keys()].map((item, pos) => (
				<Row columns={headers} key={pos} />
			))}
		</tbody>
	);
};

LoadingRow.propTypes = {
	headers: PropTypes.array.isRequired,
	per_page: PropTypes.number.isRequired,
};

export default LoadingRow;
