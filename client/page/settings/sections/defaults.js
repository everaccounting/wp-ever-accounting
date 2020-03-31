import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {
	Button,
	AccountControl,
	CompactCard,
	Card,
	Blocker,
	CurrencyControl,
	PaymentMethodControl
} from "@eaccounting/components";
import {Form, Field} from "react-final-form";


export default class Defaults extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		const {isLoading, settings} = this.props;
		return (
			<Fragment>
				<CompactCard tagName="h3">{__('Defaults Settings')}</CompactCard>
				<Card>
					<Blocker isBlocked={isLoading}>
						<Form onSubmit={this.props.onSubmit} initialValues={settings}>
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
					</Blocker>
				</Card>
			</Fragment>
		)
	}
}
