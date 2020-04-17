import {Component, Fragment} from '@wordpress/element';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {Form, Field} from "react-final-form";
import {NotificationManager} from "react-notifications";
import {
	TextControl,
	Icon,
	AccountControl,
	Card,
	CompactCard,
	Button,
	TextareaControl, PriceControl, DateControl, PaymentMethodControl
} from "@eaccounting/components";
import {withEntity} from "@eaccounting/hoc";

class EditRevenue extends Component {
	constructor(props) {
		super(props);
		this.onSubmit = this.onSubmit.bind(this);
	}


	onSubmit(form) {
		const {history, isNew} = this.props;
		form.from_account_id = form.from_account && form.from_account.id && form.from_account.id || null;
		form.to_account_id = form.to_account && form.to_account.id && form.to_account.id || null;
		delete form.from_account;
		delete form.to_account;

		this.props.handleSubmit(form, (data)=> {
			const path = isNew ? `/banking/transfers/edit/${data.id}` : `/banking/transfers`;
			history.push(path);
			const message = isNew ? __('Revenues has been inserted'): __('Revenue has been updated');
			//NotificationManager.succes(message);
		});
	}

	render() {
		const {isNew, item, settings, history} = this.props;
		const {default_payment_method} = settings;
		return (
			<Fragment>
				<CompactCard tagName="h3">{ !isNew ? __('Add Transfer') : __('Update Transfer')}</CompactCard>
				<Card>
					<Form
						onSubmit={this.onSubmit}
						initialValues={item}
						render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
							<form onSubmit={handleSubmit}>
								<div className="ea-row">
									{/*<Field*/}
									{/*	label={__('From Account', 'wp-ever-accounting')}*/}
									{/*	name="from_account"*/}
									{/*	className="ea-col-6"*/}
									{/*	before={<Icon icon={'university'}/>}*/}
									{/*	required>*/}
									{/*	{props => (*/}
									{/*		<AccountControl {...props.input} {...props}/>*/}
									{/*	)}*/}
									{/*</Field>*/}
									{/*<Field*/}
									{/*	label={__('To Account', 'wp-ever-accounting')}*/}
									{/*	name="to_account"*/}
									{/*	className="ea-col-6"*/}
									{/*	before={<Icon icon={'university'}/>}*/}
									{/*	required>*/}
									{/*	{props => (*/}
									{/*		<AccountControl {...props.input} {...props}/>*/}
									{/*	)}*/}
									{/*</Field>*/}

									{/*<Field*/}
									{/*	label={__('Amount', 'wp-ever-accounting')}*/}
									{/*	name="amount"*/}
									{/*	className="ea-col-6"*/}
									{/*	defaultValue={0}*/}
									{/*	code={values && values.from_account && values.from_account.currency && values.from_account.currency.code}*/}
									{/*	before={<Icon icon={'money'}/>}*/}
									{/*	required>*/}
									{/*	{props => (*/}
									{/*		<PriceControl {...props.input} {...props}/>*/}
									{/*	)}*/}
									{/*</Field>*/}


									{/*<Field*/}
									{/*	label={__('Date', 'wp-ever-accounting')}*/}
									{/*	name="transferred_at"*/}
									{/*	containerClass="ea-col-6"*/}
									{/*	required*/}
									{/*	before={<Icon icon={'calendar'}/>}>*/}
									{/*	{props => (*/}
									{/*		<DateControl {...props.input} {...props}/>*/}
									{/*	)}*/}
									{/*</Field>*/}

									{/*<Field*/}
									{/*	label={__('Payment Method', 'wp-ever-accounting')}*/}
									{/*	name="payment_method"*/}
									{/*	className="ea-col-6"*/}
									{/*	defaultValue={'cash'}*/}
									{/*	required*/}
									{/*	before={<Icon icon={'credit-card'}/>}>*/}
									{/*	{props => (*/}
									{/*		<PaymentMethodControl {...props.input} {...props}/>*/}
									{/*	)}*/}
									{/*</Field>*/}

									{/*<Field*/}
									{/*	label={__('Reference', 'wp-ever-accounting')}*/}
									{/*	name="reference"*/}
									{/*	className="ea-col-6"*/}
									{/*	before={<Icon icon={'file-text-o'}/>}>*/}
									{/*	{props => (*/}
									{/*		<TextControl {...props.input} {...props}/>*/}
									{/*	)}*/}
									{/*</Field>*/}

									{/*<Field*/}
									{/*	label={__('Description', 'wp-ever-accounting')}*/}
									{/*	className="ea-col-12"*/}
									{/*	name="description">*/}
									{/*	{props => (*/}
									{/*		<TextareaControl {...props.input} {...props}/>*/}
									{/*	)}*/}
									{/*</Field>*/}
								</div>
								<p style={{marginTop: '20px'}}>
									<Button
										isPrimary
										disabled={submitting || pristine}
										type="submit">{__('Submit')}
									</Button>
									<Button primary={false} onClick={()=> history.goBack()}>{__('Cancel')}</Button>
								</p>
							</form>
						)}/>
				</Card>
			</Fragment>
		)
	}
}

export default withEntity('revenues')(EditRevenue);
