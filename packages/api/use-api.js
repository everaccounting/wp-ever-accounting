/**
 * Internal dependencies
 */
import useQuery from './query';
import useMutation from './mutation';

export default {
	get: ( ...args ) => useQuery( ...args ),
	post: ( ...args ) => useMutation( 'post', ...args ),
	put: ( ...args ) => useMutation( 'put', ...args ),
	patch: ( ...args ) => useMutation( 'patch', ...args ),
	delete: ( ...args ) => useMutation( 'delete', ...args ),
	getCurrencies: ( ...args ) => useQuery( 'ea/v1/currencies/', ...args ),
	getCustomers: ( ...args ) => useQuery( 'ea/v1/customers/', ...args ),
	getVendors: ( ...args ) => useQuery( 'ea/v1/vendors/', ...args ),
};

