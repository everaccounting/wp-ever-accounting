import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {
	Button,
	TextControl,
	DateControl,
	AccountControl,
	CompactCard,
	Card,
	PriceControl,
	Spinner, SelectControl, CurrencyControl, PaymentMethodControl
} from "@eaccounting/components";
import {Form, Field} from "react-final-form";
import apiFetch from "@wordpress/api-fetch";
import {get} from "lodash";

export default class Defaults extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	getApiValue = (path) => {
		return apiFetch({path})
	};

	form() {

		return (
			<Form onSubmit={this.props.onSubmit} initialValues={this.props.settings}>
				{props => (
					<form onSubmit={props.handleSubmit}>
						<div className="ea-double-columns">
							<Field
								label={__('Default Account', 'wp-ever-accounting')}
								name="default_account"
								required>
								{props => (
									<AccountControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Default Currency', 'wp-ever-accounting')}
								name="default_currency"
								required>
								{props => (
									<CurrencyControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Payment Method', 'wp-ever-accounting')}
								name="default_payment_method"
								required>
								{props => (
									<PaymentMethodControl {...props.input} {...props}/>
								)}
							</Field>

						</div>
						<Button type="submit" isPrimary>{__('Submit', 'wp-ever-accounting')}</Button>
					</form>
				)}
			</Form>
		)
	}

	render() {
		const {isLoading} = this.props;
		return (
			<Fragment>
				<CompactCard tagName="h3">{__('Defaults Settings')}</CompactCard>
				<Card> {isLoading ? <Spinner/> : this.form()}</Card>
			</Fragment>
		)
	}
}
