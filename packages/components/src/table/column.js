/**
 * External dependencies
 */

import React from 'react';

/**
 * Internal dependencies
 */

const Column = ({name = '', className = null, children, selected}) => {
	return (
		<td className={className}>{children}</td>
	);

};

export default Column;
