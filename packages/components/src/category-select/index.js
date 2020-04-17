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
import PriceControl from '../price-control';
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {
	faFont,
	faPencilAlt,
	faMoneyBillAlt,
	faExchangeAlt
} from '@fortawesome/free-solid-svg-icons'

class CreateCategoryModal extends Component {
	render() {
		return (
			<Fragment>

				<Modal title={__('New Category')} onClose={this.props.onClose}>
					<Form
						onSubmit={this.props.onSubmit}
						initialValues={{}}
						render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
							<form onSubmit={handleSubmit}>
								<Field
									label={__('Category Name', 'wp-ever-accounting')}
									name="name"
									before={<FontAwesomeIcon icon={faFont}/>}
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


export default class CategorySelect extends Component {
	static propTypes = {
		label: PropTypes.string,
		placeholder: PropTypes.string,
		isMulti: PropTypes.bool,
		onChange: PropTypes.func,
		before: PropTypes.node,
		after: PropTypes.node,
		value: PropTypes.any,
		enableCreate: PropTypes.bool,
		type: PropTypes.any.isRequired,
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
		apiFetch({path: addQueryArgs('/ea/v1/categories', {...params, type: this.props.type})}).then(res => {
			callback(res);
		});
	}

	handleSubmit(data) {
		data.type = this.props.type;
		apiFetch({path: '/ea/v1/categories', method: 'POST', data}).then(res => {
			NotificationManager.success(sprintf(__('"%s" category created.'), res.name));
			this.setState({defaultOptions: [res, ...this.state.defaultOptions]});
			this.setState({isAdding: !this.state.isAdding});
			this.ref.current.select.select.setValue(res);
		}).catch(error => NotificationManager.error(error.message))
	}

	render() {
		return (
			<Fragment>
				{this.state.isAdding && <CreateCategoryModal
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
					noOptionsMessage={() => __('No categories')}
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

