/**
 * External dependencies
 */
import {Component, Fragment, createRef} from '@wordpress/element';
/**
 * WordPress dependencies
 */
import {__, sprintf} from '@wordpress/i18n';
/**
 * Internal dependencies
 */
import AsyncSelect from '../select-control/async';
import PropTypes from 'prop-types';
import {addQueryArgs} from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import Modal from "../modal";
import {Form, Field} from "react-final-form";
import TextControl from "../text-control";
import SelectControl from "../select-control"
import Button from "../button";
import {NotificationManager} from 'react-notifications';

import {getGlobalCurrencies} from "@eaccounting/data";

class CreateCurrencyModal extends Component {
	render() {
		const currencies = getGlobalCurrencies();
		return (
			<Fragment>

				<Modal title={__('New Currency')} onClose={this.props.onClose}>
					<Form
						onSubmit={this.props.onSubmit}
						initialValues={{}}
						render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
							<form onSubmit={handleSubmit}>
								<Field
									label={__('Name', 'wp-ever-accounting')}
									name="name"
									required>
									{props => (
										<TextControl {...props.input} {...props}/>
									)}
								</Field>

								<Field
									label={__('Code', 'wp-ever-accounting')}
									name="code"
									options={currencies}
									required>
									{props => (
										<SelectControl {...props.input} {...props}/>
									)}
								</Field>

								<Field
									label={__('Rate', 'wp-ever-accounting')}
									name="rate"
									defaultValue={1}
									parse={value => value.replace(/[^\d.]+/g, '')}
									help={__('Rate against default currency. NOTE: Default currency rate is always 1')}
									required>
									{props => (
										<TextControl {...props.input} {...props}/>
									)}
								</Field>

								<p style={{marginTop: '20px'}}>
									<Button
										isPrimary
										disabled={submitting || pristine}
										type="submit">{__('Submit')}
									</Button>
								</p>

							</form>
						)}/>
				</Modal>

			</Fragment>
		)
	}
}


export default class CurrencySelect extends Component {
	static propTypes = {
		label: PropTypes.string,
		placeholder: PropTypes.string,
		isMulti: PropTypes.bool,
		enableCreate: PropTypes.bool,
		onChange: PropTypes.func,
		before: PropTypes.node,
		after: PropTypes.node,
		value: PropTypes.any,
	};

	constructor(props) {
		super(props);
		this.state = {
			defaultOptions: [],
			isAdding: false,
		};
		this.ref = createRef();
		this.fetchAPI = this.fetchAPI.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}

	componentDidMount() {
		this.fetchAPI({}, (res) => this.setState({defaultOptions: res}))
	}

	fetchAPI(params, callback) {
		apiFetch({path: addQueryArgs('/ea/v1/currencies', params)}).then(res => {
			callback(res);
		});
	}

	handleSubmit(data) {
		apiFetch({path: '/ea/v1/currencies', method: 'POST', data}).then(res => {
			NotificationManager.success(sprintf(__('"%s" currency created.'), res.name));
			this.setState({defaultOptions: [res, ...this.state.defaultOptions]});
			this.setState({isAdding: !this.state.isAdding});
			this.ref.current.select.select.setValue(res);
		}).catch(error => NotificationManager.error(error.message))
	}

	render() {
		return (
			<Fragment>
				{this.state.isAdding && <CreateCurrencyModal
					onSubmit={this.handleSubmit}
					onClose={() => this.setState({isAdding: !this.state.isAdding})}/>}
				<AsyncSelect
					defaultOptions={this.state.defaultOptions}
					getOptionLabel={option => option && option.name && option.name}
					getOptionValue={option => option && option.code && option.code}
					loadOptions={(search, callback) => {
						this.fetchAPI({search}, callback);
					}}
					innerRef={this.ref}
					noOptionsMessage={() => __('No currencies')}
					withFooter={this.props.enableCreate}
					onAddClick={() => {
						this.ref.current.select.select.blur();
						this.setState({isAdding: !this.state.isAdding});
					}}
					{...this.props}
				/>
			</Fragment>
		);
	}
}

