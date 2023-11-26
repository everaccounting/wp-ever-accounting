/**
 * External dependencies
 */
import { Input, SectionHeader, JiraSelect, Space } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';

function Addons() {
	const [inline, setInline] = useState(false);
	const [isMulti, setIsMulti] = useState(false);
	const [selected, setSelected] = useState([]);
	return (
		<>
			<SectionHeader title={__('Addons', 'wp-ever-accounting')} />
			{JSON.stringify(selected)}
			<Space direction="vertical" size="large" style={{ width: '100%' }}>
				<Input.Switch
					label={__('Inline', 'wp-ever-accounting')}
					checked={inline}
					onChange={setInline}
				/>
				<Input.Switch
					label={__('Multi', 'wp-ever-accounting')}
					checked={isMulti}
					onChange={setIsMulti}
				/>
				<JiraSelect
					label={__('Type', 'wp-ever-accounting')}
					help={__('Select a type for this item.', 'wp-ever-accounting')}
					placeholder={__('Select a type', 'wp-ever-accounting')}
					onCreate={(name) => {
						console.log('create', name);
					}}
					onChange={setSelected}
					value={selected}
					isMulti={isMulti}
					variant={inline ? 'empty' : 'normal'}
					dropdownWidth={300}
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

export default Addons;
