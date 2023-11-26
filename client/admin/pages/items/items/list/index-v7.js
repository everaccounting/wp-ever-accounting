/**
 * External dependencies
 */
import { SectionHeader, Input, SelectControl, Select, Space, InputBase } from '@eac/components';
import { AddCategory } from '@eac/editor';
/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
/**
 * Internal dependencies
 */

function List() {
	const [addingCategory, setAddingCategory] = useState(false);
	const [single, setSingle] = useState(null);
	const [multi, setMulti] = useState([]);
	const handleAddCategory = (name) => {};
	return (
		<>
			<SectionHeader title={__('List', 'wp-ever-accounting')} />
			<Space direction="vertical" size="large" style={{ width: '100%' }}>
				<SelectControl
					label={__('Category', 'wp-ever-accounting')}
					options={[]}
					help={__('Select a category for this item.', 'wp-ever-accounting')}
				/>
				<Input
				// label={ __( 'Name', 'wp-ever-accounting' ) }
				// help={ __( 'Enter the name of the item.', 'wp-ever-accounting' ) }
				/>
				<Select
					label={__('Type', 'wp-ever-accounting')}
					help={__('Select a type for this item.', 'wp-ever-accounting')}
					placeholder={__('Select a type', 'wp-ever-accounting')}
					onCreate={(name) => {
						console.log('create', name);
					}}
					onChange={setMulti}
					value={multi}
					isMulti={true}
					options={[
						{
							label: 'Product',
							value: 'product',
						},
						{
							label: 'Service',
							value: 'service',
						},
						{
							label: 'Fee',
							value: 'fee',
						},
						{
							label: 'Discount',
							value: 'discount',
						},
					]}
				/>
				<Select
					label={__('Type', 'wp-ever-accounting')}
					help={__('Select a type for this item.', 'wp-ever-accounting')}
					placeholder={__('Select a type', 'wp-ever-accounting')}
					onCreate={(name) => {
						console.log('create', name);
					}}
					onChange={setSingle}
					value={single}
					options={[
						{
							label: 'Product',
							value: 'product',
						},
						{
							label: 'Service',
							value: 'service',
						},
						{
							label: 'Fee',
							value: 'fee',
						},
						{
							label: 'Discount',
							value: 'discount',
						},
					]}
				/>
				<Select
					variant="empty"
					label={__('Type', 'wp-ever-accounting')}
					help={__('Select a type for this item.', 'wp-ever-accounting')}
					placeholder={__('Select a type', 'wp-ever-accounting')}
					onCreate={(name) => {
						console.log('create', name);
					}}
					onChange={setMulti}
					value={multi}
					isMulti={true}
					options={[
						{
							label: 'Product',
							value: 'product',
						},
						{
							label: 'Service',
							value: 'service',
						},
						{
							label: 'Fee',
							value: 'fee',
						},
						{
							label: 'Discount',
							value: 'discount',
						},
					]}
				/>
				<Select
					variant="empty"
					label={__('Type', 'wp-ever-accounting')}
					help={__('Select a type for this item.', 'wp-ever-accounting')}
					placeholder={__('Select a type', 'wp-ever-accounting')}
					onCreate={(name) => {
						console.log('create', name);
					}}
					onChange={setSingle}
					value={single}
					options={[
						{
							label: 'Product',
							value: 'product',
						},
						{
							label: 'Service',
							value: 'service',
						},
						{
							label: 'Fee',
							value: 'fee',
						},
						{
							label: 'Discount',
							value: 'discount',
						},
					]}
				/>
			</Space>
		</>
	);
}

export default List;
