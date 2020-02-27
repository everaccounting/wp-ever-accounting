/**
 * External dependencies
 */
import {pickBy, isEqual as _IsEqual} from 'lodash';
import clean from 'lodash-clean'

/**
 * Remove empty property from object
 *
 * @param obj
 * @returns {*}
 */
export const cleanProps = (obj) => {
	return clean(obj);
};

/**
 * Pick by keys
 *
 * @param obj
 * @param keys
 * @returns {*}
 */
export const pickByKeys = (obj, keys) => {
	return pickBy(obj, "object" === typeof keys ? Object.keys(keys) : keys);
};


/**
 * Checks is equal array
 * @param obj1
 * @param obj2
 * @returns {*}
 */
export const isEqual = (obj1, obj2) => {
	return _IsEqual(obj1, obj2);
};
