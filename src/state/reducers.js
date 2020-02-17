/**
 * External dependencies
 */
import { combineReducers } from 'redux';

import accounts from 'state/accounts/reducer';

const reducer = combineReducers( {
	accounts
} );

export default reducer;
