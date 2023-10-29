export const NAMESPACE = '/eac/v1';
export const MAX_PER_PAGE = 100;
export const SECOND = 1000;
export const MINUTE = 60 * SECOND;
export const HOUR = 60 * MINUTE;
export const DAY = 24 * HOUR;
export const WEEK = 7 * DAY;
export const MONTH = ( 365 * DAY ) / 12;
export const QUERY_DEFAULTS = {
	pageSize: 25,
	period: 'month',
	compare: 'previous_year',
	noteTypes: [ 'info', 'marketing', 'survey', 'warning' ],
};
