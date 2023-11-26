/**
 * External dependencies
 */
import { SectionHeader, Select, Space, Input } from '@eac/components';
import Experimental from '@eac/experimental';
/**
 * WordPress dependencies
 */
import { useCallback, useState } from '@wordpress/element';
import { resolveSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */

const Modal = ({ createdName }) => {
	return (
		<>
			<Input.Text label={__('Name', 'wp-ever-accounting')} value={createdName} />
		</>
	);
};

function List() {
	const [showModal, setShowModal] = useState(false);
	const [createdName, setCreatedName] = useState('');
	const showSearch = false;
	const [inline, setInline] = useState(false);
	const [value, setValue] = useState('');
	const [selected, setSelected] = useState([]);
	const loadOptions = useCallback((search) => {
		return resolveSelect('eac/entities')
			.getRecords('item', {
				search,
			})
			.then((items) => {
				return items;
			});
	}, []);

	const handleCreate = async (name, callback) => {
		setCreatedName(name);
		setShowModal(true);
		// Perform any other actions or API calls related to creating the item
		// Call the callback if needed
		if (callback) {
			// Assuming you want to pass some data back to the callback
			callback(/* pass any data here */);
		}
	};

	return (
		<>
			<SectionHeader title={__('List', 'wp-ever-accounting')} />
			<Input.Switch
				label={__('Inline', 'wp-ever-accounting')}
				checked={inline}
				onChange={setInline}
			/>
			{showModal && <Modal createdName={createdName} />}
			<Experimental
				label={__('Category', 'wp-ever-accounting')}
				placeholder={__('Select a category', 'wp-ever-accounting')}
				suffix="S"
				prefix="P"
				variant={inline ? 'inline' : 'normal'}
				loadOptions={loadOptions}
				defaultOptions
				isMulti
				options={selected}
				getOptionLabel={(option) => option.name}
				getOptionValue={(option) => option.id}
				help={__('Select a category for this item.', 'wp-ever-accounting')}
				onCreate={handleCreate}
			/>
		</>
	);
}

export default List;
