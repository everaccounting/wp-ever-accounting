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
import {withSelect} from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';

class TaxRateControl extends Component {
	constructor(props) {
		super(props);
	}

	apiFetch(params, callback) {
		apiFetch({path: addQueryArgs('/ea/v1/taxrates', params)}).then(res => {
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
						this.apiFetch({search}, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		);
	}
}

export default withSelect(select => {
	return {
		defaultOptions: select('ea/collection').fetchAPI('taxrates')
	}
})(TaxRateControl)