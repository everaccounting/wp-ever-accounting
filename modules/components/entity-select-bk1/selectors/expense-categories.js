/**
 * Internal dependencies
 */
import CategoryModal from '../../forms/category';

export default {
	entityName: 'categories',
	baseQuery: { type: 'expense' },
	getOptionLabel: ( category ) => category && category.name,
	getOptionValue: ( category ) => category && category.id,
	modal: <CategoryModal type={ 'expense' } />,
};
