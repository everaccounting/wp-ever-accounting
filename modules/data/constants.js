const {default_currency} = window.eaccountingi10n;

export const API_NAMESPACE = '/ea/v1';
export const PER_PAGE = 20;
export const QUERY_DEFAULTS = {
	page: 1,
	perPage: PER_PAGE,
	orderBy: 'id',
	order: 'ASC'
}

export const DEFAULT_CURRENCY = default_currency || {
	code: 'USD',
	decimal_separator: '.',
	enabled: true,
	id: '',
	name: 'US Dollar',
	position: 'before',
	precision: 2,
	rate: '1.0000000',
	symbol: '$',
	thousand_separator: ',',
};
