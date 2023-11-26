/**
 * External dependencies
 */
import {
	SectionHeader,
	Input,
	SelectControl,
	Select as LegacySelect,
	Space,
} from '@eac/components';
import Experimental from '@eac/experimental';
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
	const [value, setValue] = useState([]);
	const [single, setSingle] = useState(null);
	const [multi, setMulti] = useState([]);
	return (
		<>
			<SectionHeader title={__('List', 'wp-ever-accounting')} />
			<Space direction="vertical" size="large" style={{ width: '100%' }}>
				<Experimental
					label={__('Category', 'wp-ever-accounting')}
					placeholder={__('Select a category', 'wp-ever-accounting')}
					suffix="S"
					prefix="P"
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
					value={value}
					onChange={setValue}
					help={__('Select a category for this item.', 'wp-ever-accounting')}
				/>
				<hr />
				<SelectControl
					label={__('Category', 'wp-ever-accounting')}
					options={[]}
					help={__('Select a category for this item.', 'wp-ever-accounting')}
				/>
				<Input
					label={__('Name', 'wp-ever-accounting')}
					help={__('Enter the name of the item.', 'wp-ever-accounting')}
				/>
				<LegacySelect
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
				<LegacySelect
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
				<LegacySelect
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
				<LegacySelect
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
