import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {withEntity} from '@eaccounting/hoc';
import {
	DateControl,
	FormCard,
	CountrySelect,
	TextareaControl,
	CurrencySelect,
	Button,
	TextControl,
	FileControl,
} from '@eaccounting/components';
import {Form, Field} from 'react-final-form';
import {get, pickBy, isObject} from 'lodash';
import {NotificationManager} from 'react-notifications';


const processFormData = data =>
	pickBy(
		{
			...data,
			file_id: get(data, 'file.id'),
		},
		value => !isObject(value)
	);


class EditCustomer extends Component {
	constructor(props) {
		super(props);
		this.onSubmit = this.onSubmit.bind(this);
	}

	onSubmit(form) {
		const {history, isAdd} = this.props;
		form.type='customer';
		this.props.handleSubmit(
			form,
			function (res) {
				NotificationManager.success(sprintf(__('Customer %s.'), isAdd ? __('created') : __('updated')));
				history.push(`/sales/customers/${res.id}/edit`);
			},
			true
		);
	}

	render() {
		const {isAdd, item, settings} = this.props;
		const {default_account, default_currency, company_country} = settings;
		return (
			<FormCard title={isAdd ? __('Add Customer') : __('Update Customer')}>
				<Form
					onSubmit={data => this.onSubmit(processFormData(data))}
					initialValues={item}
					render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
						<form onSubmit={handleSubmit} className="ea-row">

							<Field
								label={__('Name', 'wp-ever-accounting')}
								name="name"
								className="ea-col-6"
								parse={value => value}
								required>
								{props => <TextControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Currency', 'wp-ever-accounting')}
								name="currency_code"
								defaultValue={default_currency}
								className="ea-col-6"
								required>
								{props => <CurrencySelect create={true} {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Email', 'wp-ever-accounting')}
								name="email"
								type="email"
								parse={value => value}
								className="ea-col-6">
								{props => <TextControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Phone', 'wp-ever-accounting')}
								name="phone"
								type="phone"
								parse={value => value}
								className="ea-col-6">
								{props => <TextControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Fax', 'wp-ever-accounting')}
								name="fax"
								type="fax"
								parse={value => value}
								className="ea-col-6">
								{props => <TextControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Tax Number', 'wp-ever-accounting')}
								name="tax_number"
								parse={value => value}
								className="ea-col-6">
								{props => <TextControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Website', 'wp-ever-accounting')}
								name="website"
								parse={value => value}
								className="ea-col-6">
								{props => <TextControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Birth Date', 'wp-ever-accounting')}
								name="birth_date"
								parse={value => value}
								containerClass="ea-col-6">
								{props => <DateControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Note', 'wp-ever-accounting')}
								name="note"
								parse={value => value}
								className="ea-col-6">
								{props => <TextareaControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Address', 'wp-ever-accounting')}
								name="address"
								parse={value => value}
								className="ea-col-6">
								{props => <TextareaControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Country', 'wp-ever-accounting')}
								name="country"
								defaultValue={company_country}
								parse={value => value}
								className="ea-col-6">
								{props => <CountrySelect {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Photo', 'wp-ever-accounting')}
								name="file"
								parse={value => value}
								className="ea-col-6">
								{props => <FileControl {...props.input} {...props} />}
							</Field>

							<p className="ea-col-12">
								<Button isPrimary disabled={submitting || pristine} type="submit">
									{__('Submit')}
								</Button>
							</p>
						</form>
					)}
				/>
			</FormCard>
		);
	}
}

export default withEntity('contacts')(EditCustomer);
