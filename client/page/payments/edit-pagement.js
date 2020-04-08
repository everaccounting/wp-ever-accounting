import {Component, Fragment} from '@wordpress/element';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {Form, Field} from "react-final-form";
import {
	TextControl,
	Icon,
	AccountControl,
	Card,
	CompactCard,
	FileControl,
	Button,
	TextareaControl, PriceControl, DateControl, PaymentMethodControl, CategoryControl, TaxRateControl, ContactControl
} from "@eaccounting/components";
import {withEntity} from "@eaccounting/hoc";
import EditCategory from "../categories/edit-category";
class EditPayment extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isOpenCategoryModal:false
		};
		this.onSubmit = this.onSubmit.bind(this);
	}

	async onSubmit(data) {
		data.account_id = data.account && data.account.id && data.account.id;
		data.currency = data.currency && data.currency.code && data.currency.code;
		data.contact_id = data.contact && data.contact.id && data.contact.id;
		data.category_id = data.category && data.category.id && data.category.id;
		// data.file_ids = data.files.map(file => file.id).join(',');
		delete data.currency;
		delete data.account;
		delete data.contact;
		delete data.category;
		const {history, isNew} = this.props;

		this.props.handleSubmit(data, (res)=> {
			const path = isNew ? `/expenses/payments/${res.id}` : `/expenses/payments`;
			history.push(path)
		});
	}


	render() {
		const {isNew, item, settings, history} = this.props;
		const {isOpenCategoryModal} = this.state;
		const {default_payment_method, default_account} = settings;
		return (
			<Fragment>
				<CompactCard tagName="h3">{ isNew ? __('New Payment') : __('Update Payment')}</CompactCard>
				<Card>
					<Form
						onSubmit={this.onSubmit}
						initialValues={item}
						render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
							<form onSubmit={handleSubmit}>
								<div className="ea-row">

									<Field
										label={__('Date', 'wp-ever-accounting')}
										name="paid_at"
										containerClass="ea-col-6"
										before={<Icon icon={'calendar'}/>}
										required>
										{props => (
											<DateControl {...props.input} {...props}/>
										)}
									</Field>
									<Field
										label={__('Account', 'wp-ever-accounting')}
										name="account"
										className="ea-col-6"
										defaultValue={default_account}
										before={<Icon icon={'university'}/>}
										required>
										{props => (
											<AccountControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Amount', 'wp-ever-accounting')}
										name="amount"
										className="ea-col-6"
										code={values.account && values.account.currency_code && values.account.currency_code || ''}
										before={<Icon icon={'money'}/>}
										required>
										{props => (
											<PriceControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Category', 'wp-ever-accounting')}
										name="category"
										className="ea-col-6"
										before={<Icon icon={'folder-open-o'}/>}
										required>
										{props => (
											<CategoryControl type="expense" {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Vendor', 'wp-ever-accounting')}
										name="contact"
										className="ea-col-6"
										before={<Icon icon={'user'}/>}>
										{props => (
											<ContactControl {...props.input} {...props}/>
										)}
									</Field>
									<Field
										label={__('Payment Method', 'wp-ever-accounting')}
										name="payment_method"
										className="ea-col-6"
										defaultValue={default_payment_method}
										before={<Icon icon={'credit-card'}/>}
										required>
										{props => (
											<PaymentMethodControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Description', 'wp-ever-accounting')}
										className="ea-col-12"
										name="description">
										{props => (
											<TextareaControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Reference', 'wp-ever-accounting')}
										name="reference"
										className="ea-col-6"
										before={<Icon icon={'file-text-o'}/>}>
										{props => (
											<TextControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Attachment', 'wp-ever-accounting')}
										name="file"
										className="ea-col-6"
										accept={'image/*, *.pdf,*.doc'}
										hasButton={true}>
										{props => (
											<FileControl {...props.input} {...props}/>
										)}
									</Field>


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

export default withEntity('payments')(EditPayment);
