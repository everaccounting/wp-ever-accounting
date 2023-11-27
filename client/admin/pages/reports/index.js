/**
 * External dependencies
 */
import { SectionHeader, Input, Select } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { ComboboxControl } from '@wordpress/components';
import { useCallback, useState } from '@wordpress/element';
import { resolveSelect } from '@wordpress/data';

function Reports() {
	const [value, setValue] = useState('');
	const [selected, setSelected] = useState([]);
	const [inline, setInline] = useState(false);

	const loadOptions = useCallback((search) => {
		return resolveSelect('eac/entities')
			.getRecords('item', {
				search,
			})
			.then((items) => {
				return items;
			});
	}, []);

	return (
		<>
			<SectionHeader title={__('Reports', 'wp-ever-accounting')} />
			<Select
				label={__('Category', 'wp-ever-accounting')}
				placeholder={__('Select a category', 'wp-ever-accounting')}
				suffix="S"
				prefix="P"
				variant={inline ? 'inline' : 'normal'}
				loadOptions={loadOptions}
				defaultOptions
				isMulti={false}
				options={[
					{
						label: 'One',
						value: 'one',
					},
				]}
				getOptionLabel={(option) => option.name}
				getOptionValue={(option) => option.id}
				help={__('Select a category for this item.', 'wp-ever-accounting')}
			/>
			<ComboboxControl
				disable={true}
				label="Select a country"
				// onChange={function noRefCheck(){}}
				// onFilterValueChange={function noRefCheck(){}}
				options={[
					{
						label: 'Afghanistan',
						value: 'AF',
					},
					{
						label: 'Åland Islands',
						value: 'AX',
					},
					{
						label: 'Albania',
						value: 'AL',
					},
					{
						label: 'Algeria',
						value: 'DZ',
					},
					{
						label: 'American Samoa',
						value: 'AS',
					},
				]}
				value={'AF'}
			/>
		</>
	);
}

export default Reports;
