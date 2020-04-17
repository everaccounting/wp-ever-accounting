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
import {Form, Field, FormSpy} from "react-final-form";
import TextControl from "../text-control";
import Button from "../button";
import {NotificationManager} from 'react-notifications';
import CurrencySelect from '../currency-select';
import TextAreaControl from '../textarea-control';
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {
	faFont,
	faPencilAlt,
	faMoneyBillAlt,
	faExchangeAlt, faVoicemail, faPercentage, faMailBulk, faCoins, faDigitalTachograph
} from '@fortawesome/free-solid-svg-icons'

class CreateCustomerModal extends Component {
	render() {
		return (
			<Fragment>

				<Modal title={__('New Customer')} onClose={this.props.onClose}>
					<Form
						onSubmit={this.props.onSubmit}
						initialValues={{}}
						render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
							<form onSubmit={handleSubmit}>
								<div className="ea-row">
									<Field
										label={__('Name', 'wp-ever-accounting')}
										name="name"
										className="ea-col-6"
										before={<FontAwesomeIcon icon={faFont}/>}
										required>
										{props => (
											<TextControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Email', 'wp-ever-accounting')}
										name="email"
										className="ea-col-6"
										before={<FontAwesomeIcon icon={faVoicemail}/>}>
										{props => (
											<TextControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Tax Number', 'wp-ever-accounting')}
										name="tax_number"
										className="ea-col-6"
										before={<FontAwesomeIcon icon={faDigitalTachograph}/>}>
										{props => (
											<TextControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Currency', 'wp-ever-accounting')}
										name="currency"
										className="ea-col-6"
										before={<FontAwesomeIcon icon={faCoins}/>}
										required>
										{props => (
											<CurrencySelect {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Address', 'wp-ever-accounting')}
										className="ea-col-12"
										name="address">
										{props => (
											<TextAreaControl {...props.input} {...props}/>
										)}
									</Field>

									<p style={{marginTop: '20px'}} className="ea-col-6">
										<Button
											isPrimary
											disabled={submitting || pristine}
											type="submit">{__('Submit')}
										</Button>
									</p>
								</div>


								<FormSpy subscription={{values: true}}>
									{({values}) => {
										values.currency_code = values.currency && values.currency.code && values.currency.code;
										return null;
									}}
								</FormSpy>
							</form>
						)}/>
				</Modal>

			</Fragment>
		)
	}
}


export default class CustomerSelect extends Component {
	static propTypes = {
		label: PropTypes.string,
		placeholder: PropTypes.string,
		isMulti: PropTypes.bool,
		onChange: PropTypes.func,
		before: PropTypes.node,
		after: PropTypes.node,
		value: PropTypes.any,
		enableCreate: PropTypes.bool,
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
		apiFetch({path: addQueryArgs('/ea/v1/contacts', {...params, type: 'customer'})}).then(res => {
			callback(res);
		});
	}

	handleSubmit(data) {
		data.type = 'customer';
		apiFetch({path: '/ea/v1/contacts', method: 'POST', data}).then(res => {
			NotificationManager.success(sprintf(__('"%s" customer created.'), res.name));
			this.setState({defaultOptions: [res, ...this.state.defaultOptions]});
			this.setState({isAdding: !this.state.isAdding});
			this.ref.current.select.select.setValue(res);
		}).catch(error => NotificationManager.error(error.message))
	}

	render() {
		return (
			<Fragment>
				{this.state.isAdding && <CreateCustomerModal
					onSubmit={this.handleSubmit}
					onClose={() => this.setState({isAdding: !this.state.isAdding})}/>}
				<AsyncSelect
					defaultOptions={this.state.defaultOptions}
					getOptionLabel={option => option && option.name && option.name}
					getOptionValue={option => option && option.id && option.id}
					loadOptions={(search, callback) => {
						this.fetchAPI({search}, callback);
					}}
					innerRef={this.ref}
					noOptionsMessage={() => __('No customers')}
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

