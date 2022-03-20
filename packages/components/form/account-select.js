/**
 * External dependencies
 */
import { useApi } from '@eaccounting/data';

export const AccountSelect = ( props ) => {
	const [ { account, isLoading, onChange }, getAccount ] = useApi.get(
		'ea/v1/accounts/',
		{},
		{ lazy: true }
	);

	return <div>Account Input</div>;
};
