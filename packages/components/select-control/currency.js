import AsyncSelect from './async';
import {addQueryArgs} from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import {useState} from "@wordpress/element";
import {isEmpty} from "lodash";

function CurrencyControl(props) {
	const fetchAPI = (params, callback) => {
		console.log(params);
		apiFetch({path: addQueryArgs('/ea/v1/currencies', {...params})}).then(res => {
			callback(res);
		});
	}

	const LoadingIndicator = () => false;

	return (
		<AsyncSelect
			DropdownIndicator={(className) => {
				console.log(className);
			}}
			components={{ LoadingIndicator }}
			getOptionLabel={option => option && option.name && `${option.name}(${option.symbol})`}
			getOptionValue={option => option && option.id && option.id}
			loadOptions={(search, callback) => {
				fetchAPI({search}, callback);
			}}
			noOptionsMessage={(input) => {
				return input.inputValue ? __('No currency', 'wp-ever-accounting') : __('Type to search', 'wp-ever-accounting');
			}}
			{...props}
		/>
	)
}

export default CurrencyControl;
