import {Component, Fragment} from 'react';
import {__} from '@wordpress/element';
import AsyncSelect from '../select-control/async';
import PropTypes from 'prop-types';
import apiFetch from "@wordpress/api-fetch";
import { addQueryArgs } from '@wordpress/url';

export default class AccountControl extends Component {

	constructor(props) {
		super(props);
		this.state = {
			defaultOptions: [],
		};

		this.fetchAPI = this.fetchAPI.bind(this);
	}

	componentDidMount() {
		this.fetchAPI({}, options=>{
			this.setState({
				defaultOptions: options,
			});
		})
	}

	fetchAPI(params, callback) {
		apiFetch({path: addQueryArgs('/ea/v1/accounts', params)}).then(res => {
			callback(res)
		})
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
					getOptionLabel={option => option && option.name && option.name }
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
