/**
 * WordPress dependencies
 */
/**
 * Internal dependencies
 */
import VendorModal from '../../forms/vendor';

export default {
	entityName: 'vendors',
	getOptionLabel: ( customer ) => customer && customer.name,
	getOptionValue: ( customer ) => customer && customer.id,
	modal: <VendorModal />,
};
