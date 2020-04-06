import {Component, Fragment} from '@wordpress/element';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {Form, Field} from "react-final-form";
import {
	TextControl,
	Icon,
	Modal,
	Button,
	SelectControl,
	CurrencyControl,
	TextareaControl, PriceControl
} from "@eaccounting/components";

export default class EditAccount extends Component {
	static propTypes = {
		item: PropTypes.object,
		onSubmit: PropTypes.func.isRequired,
		onClose: PropTypes.func.isRequired,
		tittle: PropTypes.string.isRequired,
		buttonTittle: PropTypes.string.isRequired,
	};

	static defaultProps = {
		item: {},
	};

	constructor(props) {
		super(props);
		this.onSubmit = this.onSubmit.bind(this);
	}


	onSubmit(form){
		form.currency_code = form.currency && form.currency.code && form.currency.code || 'USD';
		delete form.currency;
		this.props.onSubmit(form);
	}

	render() {
		return (
			<Modal title={this.props.tittle} onRequestClose={this.props.onClose}>
				<Form
					onSubmit={this.onSubmit}
					initialValues={this.props.item}
					render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
						<form onSubmit={handleSubmit}>
							<Field
								label={__('Account Name', 'wp-ever-accounting')}
								name="name"
								before={<Icon icon={'id-card-o'}/>}
								required>
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Account Number', 'wp-ever-accounting')}
								name="number"
								before={<Icon icon={'pencil'}/>}>
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Account Currency', 'wp-ever-accounting')}
								name="currency"
								before={<Icon icon={'exchange'}/>}
								required>
								{props => (
									<CurrencyControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Opening Balance', 'wp-ever-accounting')}
								name="opening_balance"
								defaultValue={0}
								code={values && values.currency && values.currency.code && values.currency.code}
								before={<Icon icon={'money'}/>}
								required>
								{props => (
									<PriceControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Bank Name', 'wp-ever-accounting')}
								name="bank_name"
								before={<Icon icon={'university'}/>}>
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Bank Phone', 'wp-ever-accounting')}
								name="bank_phone"
								before={<Icon icon={'phone'}/>}>
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Bank Address', 'wp-ever-accounting')}
								name="bank_address">
								{props => (
									<TextareaControl {...props.input} {...props}/>
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
		)
	}

}
