/**
 * External dependencies
 */
// eslint-disable-next-line no-unused-vars
/**
 * Internal dependencies
 */
import { EntitySelect } from '@eaccounting/components';
import SelectControl from './select-control';
/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import customers from './customers';
export default function Overview() {
	const [ selected, setSelected ] = useState();
	return (
		<>
			<SelectControl
				multiple={ true }
				label="Single value"
				placeholder="Start typing to filter options..."
				selected={ selected }
				onChange={ ( selected ) => setSelected( selected ) }
				{ ...customers }
			/>
			<EntitySelect
				type={ 'customers' }
				label={ 'Customer' }
				creatable={ true }
			/>
			<EntitySelect
				type={ 'vendors' }
				label={ 'Vendors' }
				creatable={ true }
			/>
			<EntitySelect
				type={ 'currencies' }
				label={ 'Currencies' }
				creatable={ true }
			/>
			<EntitySelect
				type={ 'countries' }
				label={ 'Country' }
				isMulti={ true }
			/>
			<EntitySelect
				type={ 'incomeCategories' }
				label={ 'incomeCategories' }
				creatable={ true }
			/>
			<EntitySelect
				type={ 'expenseCategories' }
				label={ 'expenseCategories' }
				creatable={ true }
			/>
			<EntitySelect
				type={ 'itemCategories' }
				label={ 'itemCategories' }
				creatable={ true }
			/>
			<EntitySelect
				type={ 'accounts' }
				label={ 'Account' }
				creatable={ true }
			/>
			<EntitySelect
				type={ 'items' }
				label={ 'Items' }
				creatable={ true }
			/>
			<EntitySelect type={ 'codes' } label={ 'codes' } />
		</>
	);
}
