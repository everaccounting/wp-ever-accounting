/**
 * This replaces regex placeholders in routes with the relevant named string
 * found in the matchIds.
 *
 * Something like:
 * /contacts/(?P<id>[\d]+)
 *
 * ..ends up as:
 * /contacts/{id}
 *
 * @param {string} route     The route to manipulate
 * @param {Array}  matchIds  An array of named ids ( [ attribute_id, id ] )
 *
 * @return {string} The route with new id placeholders
 */
export const simplifyRouteWithId = (route, matchIds) => {
	if (!Array.isArray(matchIds) || matchIds.length === 0) {
		return route;
	}
	matchIds.forEach(matchId => {
		const expression = `\\(\\?P<${matchId}>.*?\\)`;
		route = route.replace(new RegExp(expression), `{${matchId}}`);
	});
	return route;
};

/**
 * Returns an array of the identifier for the named capture groups in a given
 * route.
 *
 * For example, if the route was this:
 * /ea/v1/contacts/(?P<id>[\d]+)
 *
 * ...then the following would get returned
 * ['id' ]
 *
 * @param  {string} route - The route to extract identifier names from.
 *
 * @return {Array}  An array of named route identifier names.
 */
export const getRouteIds = route => {
	const matches = route.match(/\<[a-z_]*\>/g);
	if (!Array.isArray(matches) || matches.length === 0) {
		return [];
	}
	return matches.map(match => match.replace(/<|>/g, ''));
};

/**
 *
 * @param stateSlice
 * @param ids
 * @return {string|any}
 */
export const getRouteFromResourceEntries = (stateSlice, ids = []) => {
	// convert to array for easier discovery
	stateSlice = Object.entries(stateSlice);
	const match = stateSlice.find(([, idNames]) => {
		return ids.length === idNames.length;
	});
	const [matchingRoute, routePlaceholders] = match || [];
	// if we have a matching route, let's return it.
	if (matchingRoute) {
		return ids.length === 0 ? matchingRoute : assembleRouteWithPlaceholders(matchingRoute, routePlaceholders, ids);
	}
	return '';
};

/**
 * For a given route, route parts and ids
 *
 * @param {string} route
 * @param {Array}  routePlaceholders
 * @param {Array}  ids
 * @return {string} Assembled route.
 */
const assembleRouteWithPlaceholders = (route, routePlaceholders, ids) => {
	routePlaceholders.forEach((part, index) => {
		route = route.replace(`{${part}}`, ids[index]);
	});
	return route;
};
