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
