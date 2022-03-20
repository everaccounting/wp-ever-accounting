/**
 * Internal dependencies
 */
import useQuery from './use-query';
import useMutation from './use-mutation';

export default {
	get: ( ...args ) => useQuery( ...args ),
	post: ( ...args ) => useMutation( 'post', ...args ),
	put: ( ...args ) => useMutation( 'put', ...args ),
	patch: ( ...args ) => useMutation( 'patch', ...args ),
	delete: ( ...args ) => useMutation( 'delete', ...args ),
	getCurrencies: ( ...args ) => useQuery( 'ea/v1/currencies/', ...args ),
	getCategories: ( ...args ) => useQuery( 'ea/v1/categories/', ...args ),
	getCustomers: ( ...args ) => useQuery( 'ea/v1/customers/', ...args ),
	getVendors: ( ...args ) => useQuery( 'ea/v1/vendors/', ...args ),
	getAccounts: ( ...args ) => useQuery( 'ea/v1/accounts/', ...args ),
};
