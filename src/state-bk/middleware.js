/* global eAccountingi10n */
/**
 * Internal dependencies
 */

import { ACCOUNTS_LOADING, ACCOUNTS_LOADDED } from 'state/accounts/type';
// import { REDIRECT_LOADING, REDIRECT_ITEM_SAVING } from 'state/redirect/type';
// import { GROUP_LOADING, GROUP_ITEM_SAVING } from 'state/group/type';
// import { LOG_LOADING } from 'state/log/type';
// import { ERROR_LOADING } from 'state/error/type';
import { getPluginPage, setPageUrl } from 'lib/wordpress-url';
//
const setUrlForPage = ( action, table ) => {

// 	if ( currentPage[ pluginPage ] && action === currentPage[ pluginPage ][ 0 ].find( item => item === action ) ) {
		const { orderby, order, page, per_page, filterBy, groupBy } = table;
		const query = { orderby, order, offset: page, per_page, filterBy, groupBy };
		const defaults = {
			orderby: orderby,
			order: 'desc',
			offset: 0,
			filterBy: {},
			per_page: parseInt( eAccountingi10n.per_page, 10 ),
			groupBy: '',
		};

		if ( groupBy ) {
			defaults.orderby = 'total';
		}
//
		setPageUrl( query, defaults );
// 	}
};

export const urlMiddleware = () => next => action => {
	// switch ( action.type ) {
	// 	// case REDIRECT_ITEM_SAVING:
	// 	// case GROUP_ITEM_SAVING:
	// 	// case REDIRECT_LOADING:
	// 	// case GROUP_LOADING:
	// 	// case LOG_LOADING:
	// 	case ACCOUNTS_LOADING:
	// 		setUrlForPage( action.type, action.table ? action.table : action );
	// 		break;
	// }

	return next( action );
};
