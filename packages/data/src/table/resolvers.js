import {receiveCollectionError} from './actions';
import {apiFetchWithHeaders} from "../table/controls";
import {receiveCollection} from "../table/actions";
import {addQueryArgs} from '@wordpress/url';

export function* getCollection(endpoint, query) {
	console.group("getCollection");
	console.log(endpoint);
	console.log(query);
	const queryString = addQueryArgs('', query);
	const path = '/ea/v1/' + endpoint;
	try {
		const {items = [], total = 0} = yield apiFetchWithHeaders(path + queryString);
		yield receiveCollection(endpoint, query, {
			items,
			total
		});

	} catch (error) {
		yield receiveCollectionError(endpoint, query, error);
	}
}
