/**
 * Internal dependencies
 */
import CategoryModal from '../../forms/category';

export default {
	entityName: 'categories',
	baseQuery: { type: 'income' },
	getOptionLabel: ( category ) => category && category.name,
	getOptionValue: ( category ) => category && category.id,
	modal: <CategoryModal type={ 'income' } />,
};
