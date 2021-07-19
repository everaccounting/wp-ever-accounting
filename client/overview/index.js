/**
 * External dependencies
 */
// eslint-disable-next-line no-unused-vars
/**
 * Internal dependencies
 */
import { EntitySelect } from '@eaccounting/components';
import { getQuery } from '@eaccounting/navigation';
/**
 * WordPress dependencies
 */

export default function Overview() {
	const { account_id } = getQuery();
	console.log( account_id );
	return (
		<>
			<EntitySelect
				label={ 'Account' }
				entityName={ 'accounts' }
				entity_id={ account_id }
				creatable={ true }
			/>
			<EntitySelect
				entityName={ 'customers' }
				label={ 'Customer' }
				creatable={ true }
			/>
			<EntitySelect
				entityName={ 'vendors' }
				label={ 'Vendors' }
				creatable={ true }
			/>
			<EntitySelect
				entityName={ 'currencies' }
				label={ 'Currencies' }
				creatable={ true }
			/>
			<EntitySelect
				type={ 'countries' }
				label={ 'Country' }
				isMulti={ true }
			/>
			<EntitySelect
				entityName={ 'incomeCategories' }
				label={ 'incomeCategories' }
				creatable={ true }
			/>
			<EntitySelect
				entityName={ 'expenseCategories' }
				label={ 'expenseCategories' }
				creatable={ true }
			/>
			<EntitySelect
				entityName={ 'itemCategories' }
				label={ 'itemCategories' }
				creatable={ true }
			/>
			<EntitySelect
				entityName={ 'accounts' }
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
