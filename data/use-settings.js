// export default function use
/**
 * WordPress dependencies
 */
import {useSelect, useDispatch} from '@wordpress/data';
import {useCallback} from '@wordpress/element';
/**
 * Internal dependencies
 */
import {DEFAULT_CURRENCY, STORE_KEY} from './constants';
import {isEmpty} from "lodash";


export function useSettings() {

	const settings = useSelect((select) => select(STORE_KEY).getEntities('settings'), []).reduce((result, setting) => {
		return {...result, [setting.id]: isEmpty(setting.value) && setting.default ? setting.default: setting.value };
	}, {});
	const {default_currency = 'USD', default_account} = settings;
	const updateSettings = useCallback(
		(option, value) => {
			useDispatch(STORE_KEY).saveEntity('settings', {id: option, value});
		}
	);
	const defaultCurrency = useCallback(
		() => useSelect((select) => select(STORE_KEY).getEntity('currencies', default_currency), [default_currency]) || DEFAULT_CURRENCY,
		[default_currency]
	);
	const defaultAccount = useCallback(
		() => useSelect((select) => select(STORE_KEY).getEntity('accounts',default_account), [default_account])||{},
		[default_account]
	);

	return {...settings, updateSettings, defaultCurrency:defaultCurrency(), defaultAccount:defaultAccount()}
}
