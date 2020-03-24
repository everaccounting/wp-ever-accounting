/**
 * External dependencies
 */
import {Component, Fragment} from 'react';
/**
 * WordPress dependencies
 */
import {__} from '@wordpress/element';
/**
 * Internal dependencies
 */
import AsyncSelect from '../select-control/async';
import {withSelect} from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';

class AccountControl extends Component {
	constructor(props) {
		super(props);
	}

	fetchAPI(params, callback) {
		apiFetch({path: addQueryArgs('/ea/v1/accounts', params)}).then(res => {
			callback(res);
		});
	}

	render() {
		return (
			<Fragment>
				<AsyncSelect
					defaultOptions={this.props.defaultOptions}
					getOptionLabel={option => option && option.name && option.name}
					getOptionValue={option => option && option.id && option.id}
					loadOptions={(search, callback) => {
						this.fetchAPI({search}, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		);
	}
}

export default withSelect(select => {
	return {
		defaultOptions: select('ea/store').getCollection('accounts')
	}
})(AccountControl)
