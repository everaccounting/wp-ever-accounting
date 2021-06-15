/**
 * External dependencies
 */
import {useDispatch, useSelect} from '@wordpress/data';
import {useCallback} from '@wordpress/element';
/**
 * Internal dependencies
 */
import {STORE_NAME} from './constants';
import {DEFAULT_CURRENCY} from '../constants';

/**
 * Custom react hook for shortcut methods around user.
 *
 */
export const useSettings = () => {
	const settings = useSelect((select) => {
		const {
			getOption,
			getOptions,
			hasStartedResolution,
		} = select(STORE_NAME);

		return {
			isRequesting: hasStartedResolution('getOptions'),
			options: getOptions(),
			getOption,
		};
	});

	const {updateOption: saveOption, updateOptions: saveOptions} = useDispatch(STORE_NAME)

	const updateOption = useCallback((name, value) => saveOption(name, value), []);

	const updateOptions = useCallback((options) => saveOptions(options), []);

	const {default_currency = 'USD', default_account} = settings;

	const defaultCurrency = useCallback(
		() => useSelect((select) => select('ea/entities').getEntity('currencies', default_currency), [default_currency]) || DEFAULT_CURRENCY,
		[default_currency]
	);
	const defaultAccount = useCallback(
		() => useSelect((select) => select('ea/entities').getEntity('accounts',default_account), [default_account])||{},
		[default_account]
	);

	return {
		getOption: settings.getOption,
		settings: settings.options,
		updateOption,
		updateOptions,
		isRequesting: settings.isRequesting,
		defaultCurrency:defaultCurrency(),
		defaultAccount:defaultAccount(),
	};
};
