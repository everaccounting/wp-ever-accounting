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
import {withSelect} from '@wordpress/data';

class ContactControl extends Component {
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
	}

	fetchAPI(params, callback) {
		const { type = '' } = this.props;
		apiFetch({ path: addQueryArgs('/ea/v1/contacts', { ...params, type }) }).then(res => {
			callback(res);
		});
	}

	render() {
		const { defaultOptions } = this.props;
		return (
			<Fragment>
				<AsyncSelect
					defaultOptions={defaultOptions}
					getOptionLabel={option => option && option.first_name && option.last_name && `${option.first_name} ${option.last_name}`}
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
export default withSelect(select => {
	return {
		defaultOptions: select('ea/collection').fetchAPI('contacts')
	}
})(ContactControl)
