/**
 * Exported to the `eajs` global.
 */
export { default as data } from './data';

/**
 * Server Currency data.
 */
export * from './currencies';

/**
 * Server Defaults data.
 */
export * from './defaults';

/**
 * Server Formats data.
 */
export * from './formats';

/**
 * Date data.
 */
export * from './date';

/**
 * Server Paths data.
 */
export * from './paths';

/**
 * Server locale data.
 */
export * from './locale';

/**
 * environment constant indicating development server
 */
export const __DEV__ = process.env.NODE_ENV !== 'production';
