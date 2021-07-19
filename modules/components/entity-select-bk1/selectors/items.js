/**
 * Internal dependencies
 */
import ItemModal from '../../forms/item';

export default {
	entityName: 'items',
	getOptionLabel: ( item ) => `${ item.name }`,
	getOptionValue: ( item ) => item && item.id,
	modal: <ItemModal />,
};
