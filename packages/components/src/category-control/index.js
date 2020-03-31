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
import PropTypes from 'prop-types';
import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';
import {withSelect} from '@wordpress/data';

class CategoryControl extends Component {
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
	}

	fetchAPI(params, callback) {
		const {type = ''} = this.props;
		apiFetch({path: addQueryArgs('/ea/v1/categories', {...params, type})}).then(res => {
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
		defaultOptions: select('ea/collection').fetchAPI('categories')
	}
})(CategoryControl)
