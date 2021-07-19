/**
 * Internal dependencies
 */
import CustomerModal from '../../forms/customer';
/**
 * WordPress dependencies
 */

export default {
	entityName: 'customers',
	getOptionLabel: ( customer ) => customer && customer.name,
	getOptionValue: ( customer ) => customer && customer.id,
	modal: <CustomerModal />,
};
