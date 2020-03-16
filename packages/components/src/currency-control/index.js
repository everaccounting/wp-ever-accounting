/**
 * External dependencies
 */
import { Component, Fragment } from 'react';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/element';
/**
 * Internal dependencies
 */
import AsyncSelect from '../select-control/async';
import PropTypes from 'prop-types';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

export default class CurrenciesControl extends Component {
	static propTypes = {
		label: PropTypes.string,
		placeholder: PropTypes.string,
		isMulti: PropTypes.bool,
		onChange: PropTypes.func,
		before: PropTypes.node,
		after: PropTypes.node,
		value: PropTypes.any,
	};

	constructor(props) {
		super(props);
		this.state = {
			defaultOptions: [],
		};

		this.fetchAPI = this.fetchAPI.bind(this);
	}

	componentDidMount() {
		this.fetchAPI({}, options => {
			this.setState({
				defaultOptions: options,
			});
		});
	}

	fetchAPI(params, callback) {
		apiFetch({ path: addQueryArgs('/ea/v1/currencies', params) }).then(res => {
			callback(res);
		});
	}

	render() {
		const { defaultOptions } = this.state;
		return (
			<Fragment>
				<AsyncSelect
					defaultOptions={defaultOptions}
					noOptionsMessage={() => {
						__('No items');
					}}
					getOptionLabel={option => option && option.name && option.name}
					getOptionValue={option => option && option.code && option.code}
					loadOptions={(search, callback) => {
						this.getCurrencies({ search }, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		);
	}
}
