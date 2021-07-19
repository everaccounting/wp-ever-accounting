/**
 * Internal dependencies
 */
import AccountModal from '../../forms/account';

export default {
	entityName: 'accounts',
	getOptionLabel: ( account ) =>
		`${ account.name } (${ account.currency.code })`,
	getOptionValue: ( account ) => account && account.id,
	modal: <AccountModal />,
};
