/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

function Addons() {
	// const onClick = (record) => {
	// 	// the number part from the name.
	// 	const id = isNaN(parseInt(record.name, 10)) ? 0 : parseInt(record.name, 10);
	// 	records.updateRecord(record.id, { ...record, name: `${record.name} - edited` });
	// };
	const onDelete = (record) => {
		entities.deleteRecord(record.id);
	};

	return (
		<>
			<SectionHeader title={__('Addons', 'wp-ever-accounting')} />
		</>
	);
}

export default Addons;
