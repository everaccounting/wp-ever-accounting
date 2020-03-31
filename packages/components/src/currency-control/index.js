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
import PropTypes from 'prop-types';
import {withSelect} from '@wordpress/data';
import {addQueryArgs} from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';

class CurrencyControl extends Component {
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
	}

	fetchAPI(params, callback) {
		apiFetch({path: addQueryArgs('/ea/v1/currencies', params)}).then(res => {
			callback(res);
		});
	}

	render() {
		return (
			<Fragment>
				<AsyncSelect
					defaultOptions={this.props.defaultOptions}
					getOptionLabel={option => option && option.name && option.name}
					getOptionValue={option => option && option.code && option.code}
					loadOptions={(search, callback) => {
						this.fetchAPI({search}, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		);
	}
}


export default withSelect((select, ownProps) => {
	return {
		defaultOptions: select('ea/collection').fetchAPI('currencies', {per_page:100})
	}
})(CurrencyControl)

