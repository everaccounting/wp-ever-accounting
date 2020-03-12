import {receiveCollectionError} from './actions';
import {apiFetchWithHeaders} from "../table/controls";
import {receiveCollection} from "../table/actions";
import {addQueryArgs} from '@wordpress/url';

export function* getCollection(endpoint, query) {
	const queryString = addQueryArgs('', query);
	const path = '/ea/v1/' + endpoint;
	try {
		const {items = [], headers = {}} = yield apiFetchWithHeaders(path + queryString);
		yield receiveCollection(endpoint, queryString, {
			items,
			headers
		});

	} catch (error) {
		yield receiveCollectionError(endpoint, query, error);
	}
}
