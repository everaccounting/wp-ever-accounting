import {receiveResponse, receiveError} from "./actions";
import {fetchFromAPI} from "./controls";
import { dispatch } from '@wordpress/data-controls';
import {TABLE_STORE_KEY} from './index';

// function* invalidateCollection() {
// 	yield dispatch( TABLE_STORE_KEY, 'invalidateResolutionForStore' );
// 	return '';
// }
//
// export function* getItems(endpoint, query) {
// 	try {
// 		const {items = [], headers} = yield fetchFromAPI(endpoint, query);
// 		//yield invalidateCollection();
// 		yield receiveResponse(items, headers);
// 	} catch (error) {
// 		yield receiveError(
// 			endpoint,
// 			query,
// 			error
// 		);
// 	}
// }
