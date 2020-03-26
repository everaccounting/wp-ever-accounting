/**
 * Exported to the `eajs` global.
 */
export { default as data } from './data';

/**
 * Server locale configuration.
 */
export { locale as SERVER_LOCALE } from './locale';

/**
 * Currency Configuration for the default currency from the server
 */
export { currencyConfig as CURRENCY_CONFIG } from './currency_config';

/**
 * Default timezone configuration for the default timezone settings from the
 * server
 */
export { timezoneConfig as TIMEZONE_CONFIG } from './timezone-config';

/**
 * Miscellaneous Data from the server
 */
export * from './miscellaneous';

/**
 * Routes
 */
export * from './routes';

/**
 * Custom validators
 */
export * from './validators';

/**
 * Custom exceptions
 */
export * from './exceptions';


/**
 * environment constant indicating development server
 */
export const __DEV__ = process.env.NODE_ENV !== 'production';
