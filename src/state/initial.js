import {getInitialAccounts} from './accounts/intial';

export function initialActions(store) {
	return store;
}

export function getInitialState() {
	return {
		accounts: getInitialAccounts()
	};
}
