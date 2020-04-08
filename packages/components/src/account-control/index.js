/**
 * External dependencies
 */
import {Component, Fragment} from 'react';
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */
import AsyncSelect from '../select-control/async';
import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';

export default class AccountControl extends Component {
	constructor(props) {
		super(props);
		this.state = {
			defaults : []
		};
		this.apiFetch = this.apiFetch.bind(this);
	}

	componentDidMount() {
		apiFetch({path: '/ea/v1/accounts'}).then(defaults => {
			this.setState({
				defaults
			})
		})
	}

	apiFetch(params, callback) {
		apiFetch({path: addQueryArgs('/ea/v1/accounts', params)}).then(res => {
			callback(res);
		});
	}

	render() {
		return (
			<Fragment>
				<AsyncSelect
					defaultOptions={this.state.defaults}
					getOptionLabel={option => option && option.name && option.name}
					getOptionValue={option => option && option.id && option.id}
					loadOptions={(search, callback) => {
						this.apiFetch({search}, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		);
	}
}

