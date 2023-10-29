/**
 * Returns query needed for a request to populate a table.
 *
 * @param {Object} options                  arguments
 * @param {string} options.endpoint         Report API Endpoint
 * @param {Object} options.query            Query parameters in the url
 * @param {Object} options.tableQuery       Query parameters specific for that endpoint
 * @param {string} options.defaultDateRange User specified default date range.
 * @return {Object} Object    Table data response
 */
export function getTableQuery( options ) {
	const { query, tableQuery } = options;
	return {
		orderby: query.orderby || 'date',
		order: query.order || 'desc',
		page: query.page || 1,
		per_page: query.per_page || 20,
		...tableQuery,
	};
}
