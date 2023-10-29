/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function List() {
	return (
		<>
			<SectionHeader title={__('List', 'wp-ever-accounting')} />
		</>
	);
}

export default List;
