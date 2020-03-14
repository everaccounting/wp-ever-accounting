import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {
	Form,
	TextControl,
	Button,
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
			settings: {
				data: {},
				isLoading: true
			}
		};
		this.getSettings = this.getSettings.bind(this);
	}

	componentDidMount() {
		this.getSettings();
	}

	getSettings() {
		apiFetch({path: '/ea/v1/settings/general'}).then(res => {
			this.setState({
				data: res,
				isLoading: false,
			})
		})
	}

	validate = values => {
		const errors = {};
		// console.log(values);
		// if (!values.owner_id) {
		// 	errors.owner_id = __('Default Owner is required', 'wp-ever-crm');
		// }
		// if (values.life_stage.length < 3) {
		// 	errors.life_stage = __('Default life stage is required', 'wp-ever-crm');
		// }
		return errors;
	};


	onSubmit = (ev) => {
		console.log(ev);
	};

	render() {
		const disabled = false;
		return (
			<Fragment>
				<CompactCard tagName="h3">{__('General Settings')}</CompactCard>
				<Card>
					{this.state.isLoading && <Spinner/>}
					{!this.state.isLoading &&
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
								</Row>
							</Fragment>
							// <div className="ea-row">
							// 	<div className="ea-col-6">
							// 		<TextControl
							// 			label={__('Company Name', 'wp-ever-accounting')}
							// 			required
							// 			{...getInputProps('company_name')}
							// 		/>
							// 	</div>
							// 	<div className="ea-col-6">
							// 		<TextControl
							// 			label={__('Company Email', 'wp-ever-accounting')}
							// 			required
							// 			{...getInputProps('company_email')}
							// 		/>
							// 	</div>
							//
							//
							// 	<div className="ea-col-6">
							// 		<TextControl
							// 			label={__('Tax Number', 'wp-ever-accounting')}
							// 			{...getInputProps('company_email')}
							// 		/>
							// 	</div>


						{/*<div className="ea-col-6">*/}
						{/*	<DateControl*/}
						{/*		label={"Financial Year Start"}*/}
						{/*		before={<Icon icon="calendar-check-o"/>}*/}
						{/*		required*/}

						{/*	/>*/}
						{/*</div>*/}
						{/*<div className="ea-col-6">*/}
						{/*	<Select*/}
						{/*		label={"Date Format"}*/}
						{/*		options={[*/}
						{/*			{*/}
						{/*				label: "1",*/}
						{/*				value: "1"*/}
						{/*			}*/}
						{/*		]}*/}
						{/*		before={<Icon icon="calendar"/>}*/}
						{/*	/>*/}
						{/*</div>*/}
						{/*<div className="ea-col-6">*/}
						{/*	<Select*/}
						{/*		label={"Date Separator"}*/}
						{/*		options={[*/}
						{/*			{*/}
						{/*				label: "1",*/}
						{/*				value: "1"*/}
						{/*			}*/}
						{/*		]}*/}
						{/*		before={<Icon icon="minus"/>}*/}
						{/*	/>*/}
						{/*</div>*/}
						{/*<div className="ea-col-6">*/}
						{/*	<Select*/}
						{/*		label={"Percent (%) Position"}*/}
						{/*		options={[*/}
						{/*			{*/}
						{/*				label: "1",*/}
						{/*				value: "1"*/}
						{/*			}*/}
						{/*		]}*/}
						{/*		before={<Icon icon="percent"/>}*/}
						{/*	/>*/}
						{/*</div>*/}

							// 	<div className="ea-col-12">
							// 		<Button isPrimary isBusy={disabled} onClick={handleSubmit}
							// 				disabled={Object.keys(errors).length}>
							// 			{__('Submit', 'wp-ever-accounting')}
							// 		</Button>
							// 	</div>
							// </div>
							)}
					</Form>
					}
				</Card>
			</Fragment>
		);
	}
}
