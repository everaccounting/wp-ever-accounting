import {Component, Fragment} from 'react';
import {__, sprintf} from '@wordpress/i18n';
import {withEntity} from '@eaccounting/hoc';
import {
	FormCard,
	CurrencySelect,
	PriceControl,
	TextareaControl,
	TextControl,
	BackButton,
	Button,
} from '@eaccounting/components';
import {Form, Field} from 'react-final-form';
import {NotificationManager} from 'react-notifications';
import {pickBy, isObject} from 'lodash';


class EditAccount extends Component {
	constructor(props) {
		super(props);
		this.onSubmit = this.onSubmit.bind(this);
	}

	onSubmit(form) {
		const {history, isAdd} = this.props;
		this.props.handleSubmit(form, function (res) {
			NotificationManager.success(sprintf(__('"%s" account %s.'), res.name, isAdd ? __('created') : __('updated')));
			history.push(`/banking/accounts/${res.id}/edit`);
		});
	}

	render() {
		const {isAdd, item, settings} = this.props;
		const {default_currency} = settings;
		return (
			<Fragment>
				<FormCard title={isAdd ? __('Add Account') : __('Update Account')}>
					<Form
						onSubmit={data => this.onSubmit(pickBy(data, value => !isObject(value)))}
						initialValues={item}
						render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
							<form onSubmit={handleSubmit} className="ea-row">
								<Field label={__('Account Name', 'wp-ever-accounting')} name="name" className="ea-col-6"
									   required>
									{props => <TextControl {...props.input} {...props} />}
								</Field>

								<Field
									label={__('Account Number', 'wp-ever-accounting')}
									className="ea-col-6"
									parse={value => value}
									name="number"
									required
								>
									{props => <TextControl {...props.input} {...props} />}
								</Field>

								<Field
									label={__('Account Currency', 'wp-ever-accounting')}
									name="currency_code"
									className="ea-col-6"
									defaultValue={default_currency}
									parse={value => value}
									create={true}
									required
								>
									{props => <CurrencySelect {...props.input} {...props} />}
								</Field>

								<Field
									label={__('Opening Balance', 'wp-ever-accounting')}
									name="opening_balance"
									defaultValue={0}
									parse={value => value}
									className="ea-col-6"
									code={values && values.currency && values.currency.code && values.currency.code}
									required
								>
									{props => <PriceControl {...props.input} {...props} />}
								</Field>

								<Field
									label={__('Bank Name', 'wp-ever-accounting')}
									className="ea-col-6"
									parse={value => value}
									name="bank_name"
								>
									{props => <TextControl {...props.input} {...props} />}
								</Field>

								<Field
									label={__('Bank Phone', 'wp-ever-accounting')}
									className="ea-col-6"
									parse={value => value}
									name="bank_phone"
								>
									{props => <TextControl {...props.input} {...props} />}
								</Field>

								<Field
									label={__('Bank Address', 'wp-ever-accounting')}
									className="ea-col-12"
									parse={value => value}
									name="bank_address"
								>
									{props => <TextareaControl {...props.input} {...props} />}
								</Field>

								<p className="ea-col-12">
									<Button isPrimary disabled={submitting || pristine} type="submit">
										{__('Submit')}
									</Button>

									<BackButton>{__('Cancel')}</BackButton>
								</p>
							</form>
						)}
					/>
				</FormCard>
			</Fragment>
		);
	}
}

export default withEntity('accounts')(EditAccount);
