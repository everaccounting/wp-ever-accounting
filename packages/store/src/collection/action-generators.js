import {resolveSelect, fetch} from '../base-controls';
import {resetAllState} from "./actions";
import {REDUCER_KEY as SCHEMA_REDUCER_KEY} from '../schema/constants';

/**
 * Action generator yielding actions for queuing an entity delete record
 * in the state.
 *
 * @param {string} resourceName
 * @param {number} entityId
 * @param {boolean} refresh
 */
export function* deleteEntityById(resourceName, entityId, refresh = true) {
	const route = yield resolveSelect(SCHEMA_REDUCER_KEY, 'getRoute', resourceName, [entityId]);
	const item = yield fetch({path: route, method: 'DELETE'});
	if (refresh)
		yield resetAllState();
	return item;
}
