import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {Form, Field} from "react-final-form";
import {
	TextareaControl,
	TextControl,
	DateControl,
	DatePicker,
	DateFilter,
	Card,
	CompactCard,
	Button,
	PriceControl,
	CurrencySelect,
	AccountSelect,
	CountrySelect,
	PaymentMethodSelect,
	CategorySelect,
	CustomerSelect,
	VendorSelect,
	SectionTitle,
	EmptyContent,
	ActionPanel,

} from "@eaccounting/components";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faCoffee, faFont } from '@fortawesome/free-solid-svg-icons'

export default class Example extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Fragment>
				<SectionTitle title={'I am title'}>I am content</SectionTitle>
				<h1 className="wp-heading-inline">{__('Fields')}</h1>
				<Form
					onSubmit={form => console.log(form)}
					initialValues={{}}
					render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
						<form onSubmit={handleSubmit}>
							<Card>
								<Field
									name='text_field'
									label={__('This is label')}
									before={<FontAwesomeIcon icon={faCoffee}/>}
									after='after'
									placeholder={__('Placeholder')}
									help={__('This is description')}>
									{props => (
										<TextControl {...props.input} {...props}/>
									)}
								</Field>

								<Field
									name='textarea'
									label={__('This is label')}
									placeholder={__('Placeholder')}
									help={__('This is description')}>
									{props => (
										<TextareaControl {...props.input} {...props}/>
									)}
								</Field>

								<Field
									name='wp_date'
									label={__('WordPress date picker')}
									placeholder={__('Placeholder')}
									help={__('This is description')}
									before='Before'
									after='after'>
									{props => (
										<DateControl {...props.input} {...props}/>
									)}
								</Field>

								<Field
									name='amount'
									label={__('Amount')}
									placeholder={__('Placeholder')}
									help={__('This is description')}
									before='Before'
									after='after'>
									{props => (
										<PriceControl {...props.input} {...props}/>
									)}
								</Field>

								<Field
									name='currency'
									label={__('Currency Pick')}
									placeholder={__('Placeholder')}
									help={__('This is description')}
									before={<FontAwesomeIcon icon={faFont}/>}
									enableCreate={true}
									after='after'>
									{props => (
										<CurrencySelect {...props.input} {...props}/>
									)}
								</Field>

								<Field
									name='account'
									label={__('Account')}
									placeholder={__('Placeholder')}
									help={__('This is description')}
									before='Before'
									enableCreate={true}
									after='after'>
									{props => (
										<AccountSelect {...props.input} {...props}/>
									)}
								</Field>

								<Field
									name='account_multi'
									label={__('Account')}
									placeholder={__('Placeholder')}
									help={__('This is description')}
									isMulti={true}
									before='Before'
									enableCreate={true}
									after='after'>
									{props => (
										<AccountSelect {...props.input} {...props}/>
									)}
								</Field>

								<Field
									name='country'
									label={__('Country')}
									placeholder={__('Placeholder')}
									help={__('This is description')}
									before='Before'
									defaultValue={"BD"}
									enableCreate={true}
									after='after'>
									{props => (
										<CountrySelect {...props.input} {...props}/>
									)}
								</Field>

								<Field
									name='payment_method'
									label={__('Payment Method')}
									placeholder={__('Placeholder')}
									help={__('This is description')}
									before='Before'
									defaultValue={"check"}
									enableCreate={true}
									after='after'>
									{props => (
										<PaymentMethodSelect {...props.input} {...props}/>
									)}
								</Field>

								<Field
									name='customer'
									label={__('Customer')}
									placeholder={__('Placeholder')}
									help={__('This is description')}
									before='Before'
									type="customer"
									defaultValue={"check"}
									enableCreate={true}
									after='after'>
									{props => (
										<CustomerSelect {...props.input} {...props}/>
									)}
								</Field>

								<Field
									name='category_income'
									label={__('Category Income')}
									placeholder={__('Placeholder')}
									help={__('This is description')}
									before='Before'
									type={'income'}
									defaultValue={"check"}
									enableCreate={true}
									after='after'>
									{props => (
										<CategorySelect {...props.input} {...props}/>
									)}
								</Field>

								<Field
									name='vendor'
									label={__('Vendor')}
									placeholder={__('Placeholder')}
									help={__('This is description')}
									before='Before'
									defaultValue={"check"}
									enableCreate={true}
									after='after'>
									{props => (
										<VendorSelect {...props.input} {...props}/>
									)}
								</Field>

								<p>
									<Button
										isPrimary
										disabled={submitting || pristine}
										type="submit">{__('Submit')}
									</Button>

									<Button
										secondary
										disabled={submitting || pristine}
										type="submit">{__('Submit')}
									</Button>
								</p>

							</Card>
						</form>
					)}/>


				<EmptyContent titile={'Hello'} subtitle={"lorem10"}/>
				<ActionPanel titile={'Hello'}>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio, tempore.</ActionPanel>
			</Fragment>
		);
	}
}
