/**
 * External dependencies
 */

import React from 'react';
import { translate as __ } from 'lib/locale';

const EmptyRow = props => {
	const { headers } = props;

	return (
		<tbody>
			<tr>
				<td colSpan={headers.length}>{__('No results')}</td>
			</tr>
		</tbody>
	);
};

export default EmptyRow;
