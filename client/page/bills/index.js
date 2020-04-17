import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {Field, Form, FormSpy} from "react-final-form";
import {
	ContactControl,
	CompactCard,
	Card,
	Icon,
	CurrencyControl,
	DateControl,
	TextControl
} from "@eaccounting/components";

export default class Bills extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	onSubmit(data) {
		console.log(data);
	}

	render() {
		return (
			<Fragment>
				<CompactCard>{__('New Bill')}</CompactCard>
				<Card>
					<Form
						onSubmit={this.onSubmit}
						initialValues={{}}
						render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
							<form onSubmit={handleSubmit}>
								<div className="ea-row">
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
										label={__('Currency', 'wp-ever-accounting')}
										name="currency"
										className="ea-col-6"
										before={<Icon icon={'user'}/>}>
										{props => (
											<CurrencyControl {...props.input} {...props}/>
										)}
									</Field>


									<Field
										label={__('Bill Date', 'wp-ever-accounting')}
										name="bill_date"
										containerClass="ea-col-6"
										before={<Icon icon={'calendar'}/>}
										required>
										{props => (
											<DateControl {...props.input} {...props}/>
										)}
									</Field>


									<Field
										label={__('Due Date', 'wp-ever-accounting')}
										name="due_date"
										containerClass="ea-col-6"
										before={<Icon icon={'calendar'}/>}
										required>
										{props => (
											<DateControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Bill Number', 'wp-ever-accounting')}
										name="bill_number"
										className="ea-col-6"
										before={<Icon icon={'calendar'}/>}
										required>
										{props => (
											<TextControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Order Number', 'wp-ever-accounting')}
										name="order_number"
										className="ea-col-6"
										before={<Icon icon={'calendar'}/>}
										required>
										{props => (
											<TextControl {...props.input} {...props}/>
										)}
									</Field>

									<FormSpy subscription={{values: true}}>
										{({values}) => {
											values.currency_code = values.currency && values.currency.code && values.currency.code;
											return null;
										}}

										{/*{({values}) => (*/}
										{/*	<pre>*/}
										{/*	{JSON.stringify(values, 0, 2)}*/}
										{/*	</pre>*/}
										{/*)}*/}
									</FormSpy>


								</div>
							</form>
						)}/>
				</Card>
			</Fragment>
		);
	}
}
