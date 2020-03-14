import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {
	Form,
	Button,
	TextControl,
	DateControl,
	Icon,
	CompactCard,
	Card,
	Select,
	Spinner,
	Row,
	Col
} from "@eaccounting/components";
import apiFetch from "@wordpress/api-fetch";

export default class General extends Component {
	constructor(props) {
		super(props);
		this.state = {
			data: {},
			isLoading: true
		};
		this.getSettings = this.getSettings.bind(this);
	}

	componentDidMount() {
		this.getSettings();
	}

	getSettings() {
		apiFetch({path: '/ea/v1/settings/general'}).then(res => {
			console.log('received');
			this.setState({
				data: res,
				isLoading: !this.state.isLoading
			})
		})
	}

	validate = values => {
		const errors = {};
		if (!values.company_name) {
			errors.company_name = __('Company Name required', 'wp-ever-accounting');
		}
		if (!values.company_email) {
			errors.company_email = __('Company Email required', 'wp-ever-accounting');
		}
		return errors;
	};


	onSubmit = (ev) => {
		console.log(ev);
	};

	render() {
		const disabled = false;
		const {isLoading} = this.state;
		return (
			<Fragment>
				<CompactCard tagName="h3">{__('General Settings')}</CompactCard>
				<Card>
					{this.state.isLoading ? <Spinner/> :
						<Form validate={this.validate} onSubmitCallback={this.onSubmit} initialValues={this.state.data}>
							{({getInputProps, values, errors, handleSubmit}) => (
								<Fragment>
									<Row>
										<Col>
											<TextControl
												label={__('Company Name', 'wp-ever-accounting')}
												required
												{...getInputProps('company_name')}
											/>
										</Col>
										<Col>
											<TextControl
												label={__('Company Email', 'wp-ever-accounting')}
												required
												{...getInputProps('company_email')}
											/>
										</Col>

										<Col>
											<TextControl
												label={__('Tax Number', 'wp-ever-accounting')}
												{...getInputProps('company_tax_number')}
											/>
										</Col>

										<Col>
											<TextControl
												label={__('Phone Number', 'wp-ever-accounting')}
												{...getInputProps('company_phone')}
											/>
										</Col>

										<Col>
											<TextControl
												label={__('Address', 'wp-ever-accounting')}
												{...getInputProps('company_address')}
											/>
										</Col>

										<Col>
											<TextControl
												label={__('City', 'wp-ever-accounting')}
												{...getInputProps('company_city')}
											/>
										</Col>

										<Col>
											<TextControl
												label={__('State', 'wp-ever-accounting')}
												{...getInputProps('company_state')}
											/>
										</Col>

										<Col>
											<TextControl
												label={__('Postcode', 'wp-ever-accounting')}
												{...getInputProps('company_postcode')}
											/>
										</Col>

										<Col>
											<TextControl
												label={__('Country', 'wp-ever-accounting')}
												{...getInputProps('company_country')}
											/>
										</Col>

										<Col col={12}>
											<Button isPrimary isBusy={disabled} onClick={handleSubmit}
													disabled={Object.keys(errors).length}>
												{__('Submit', 'wp-ever-accounting')}
											</Button>
										</Col>
									</Row>
								</Fragment>
							)}
						</Form>
					}
				</Card>
			</Fragment>
		);
	}
}
