import {isEqual, isObjectLike} from "lodash";


/**
 * Given a function, returns an enhanced function which caches the result and
 * tracks in WeakMap. The result is only cached if the original function is
 * passed a valid object-like argument (requirement for WeakMap key).
 *
 * @param {Function} fn Original function.
 *
 * @return {Function} Enhanced caching function.
 */
export function withWeakMapCache(fn) {
	const cache = new WeakMap();

	return (key) => {
		let value;
		if (cache.has(key)) {
			value = cache.get(key);
		} else {
			value = fn(key);

			// Can reach here if key is not valid for WeakMap, since `has`
			// will return false for invalid key. Since `set` will throw,
			// ensure that key is valid before setting into cache.
			if (isObjectLike(key)) {
				cache.set(key, value);
			}
		}

		return value;
	};
}

/**
 * Given a value which can be specified as one or the other of a comma-separated
 * string or an array, returns a value normalized to an array of strings, or
 * null if the value cannot be interpreted as either.
 *
 * @param {string|string[]|*} value
 *
 * @return {?(string[])} Normalized field value.
 */
export function getNormalizedCommaSeparable(value) {
	if (typeof value === 'string') {
		return value.split(',');
	} else if (Array.isArray(value)) {
		return value;
	}

	return null;
}


/**
 * Given the current and next item entity, returns the minimally "modified"
 * result of the next item, preferring value references from the original item
 * if equal. If all values match, the original item is returned.
 *
 * @param {Object} item     Original item.
 * @param {Object} nextItem Next item.
 *
 * @return {Object} Minimally modified merged item.
 */
export function conservativeMapItem(item, nextItem) {
	// Return next item in its entirety if there is no original item.
	if (!item) {
		return nextItem;
	}

	let hasChanges = false;
	const result = {};
	for (const key in nextItem) {
		if (isEqual(item[key], nextItem[key])) {
			result[key] = item[key];
		} else {
			hasChanges = true;
			result[key] = nextItem[key];
		}
	}

	if (!hasChanges) {
		return item;
	}

	// Only at this point, backfill properties from the original item which
	// weren't explicitly set into the result above. This is an optimization
	// to allow `hasChanges` to return early.
	for (const key in item) {
		if (!result.hasOwnProperty(key)) {
			result[key] = item[key];
		}
	}

	return result;
}

/**
 * A higher-order reducer creator which invokes the original reducer only if
 * the dispatching action matches the given predicate, **OR** if state is
 * initializing (undefined).
 *
 * @param {Function} isMatch Function predicate for allowing reducer call.
 *
 * @return {Function} Higher-order reducer.
 */
export const ifMatchingAction = (isMatch) => (reducer) => (state, action) => {
	if (state === undefined || isMatch(action)) {
		return reducer(state, action);
	}

	return state;
};

/**
 * Higher-order reducer creator which creates a combined reducer object, keyed
 * by a property on the action object.
 *
 * @param {string} actionProperty Action property by which to key object.
 *
 * @return {Function} Higher-order reducer.
 */
export const onSubKey = (actionProperty) => (reducer) => (
	state = {},
	action
) => {
	// Retrieve subkey from action. Do not track if undefined; useful for cases
	// where reducer is scoped by action shape.
	const key = action[actionProperty];
	if (key === undefined) {
		return state;
	}

	// Avoid updating state if unchanged. Note that this also accounts for a
	// reducer which returns undefined on a key which is not yet tracked.
	const nextKeyState = reducer(state[key], action);
	if (nextKeyState === state[key]) {
		return state;
	}

	return {
		...state,
		[key]: nextKeyState,
	};
};

/**
 * Higher-order reducer creator which substitutes the action object before
 * passing to the original reducer.
 *
 * @param {Function} replacer Function mapping original action to replacement.
 *
 * @return {Function} Higher-order reducer.
 */
export const replaceAction = (replacer) => (reducer) => (state, action) => {
	return reducer(state, replacer(action));
};
