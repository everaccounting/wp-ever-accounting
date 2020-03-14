/**
 * External dependencies
 */

import React from 'react';
import { __ } from '@wordpress/i18n';

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
