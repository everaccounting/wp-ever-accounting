/**
 * The reducer key used by core data in store registration.
 * This is defined in a separate file to avoid cycle-dependency
 *
 * @type {string}
 */
export const STORE_KEY = 'ea/data';
export const API_NAMESPACE = '/ea/v1';
export const DEFAULT_CURRENCY = {
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
