/**
 * Merge Query
 * @param oldQuery
 * @param newQuery
 * @returns {any}
 */
export const mergeQuery = (oldQuery, newQuery) => {
	const queryParams = ['orderby', 'order', 'page', 'per_page', 'filters'];
	const data = Object.assign({}, oldQuery);
	for (let x = 0; x < queryParams.length; x++) {
		if (newQuery[queryParams[x]] !== undefined) {
			data[queryParams[x]] = newQuery[queryParams[x]];
		}
	}
	return data;
};

export const removeDefaultQueries = (query, defaultOrderBy='id') => {
	if (query.order === 'desc') {
		delete query.order;
	}

	if (query.orderby === defaultOrderBy) {
		delete query.orderby;
	}

	if (query.page === 1) {
		delete query.page;
	}

	if (query.per_page === 20) {
		delete query.per_page;
	}

	delete query.selected;

	return query;
};
