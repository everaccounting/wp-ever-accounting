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

export default class ContactControl extends Component {
	static propTypes = {
		label: PropTypes.string,
		placeholder: PropTypes.string,
		isMulti: PropTypes.bool,
		onChange: PropTypes.func,
		before: PropTypes.node,
		after: PropTypes.node,
		type: PropTypes.any,
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
		const { type = '' } = this.props;
		apiFetch({ path: addQueryArgs('/ea/v1/contacts', { ...params, type }) }).then(res => {
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
					getOptionValue={option => option && option.id && option.id}
					loadOptions={(search, callback) => {
						this.fetchAPI({ search }, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		);
	}
}
