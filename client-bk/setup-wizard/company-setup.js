/**
 * External dependencies
 */
import { useSettings } from '@eaccounting/data';
/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
import { DatePicker, Form, TextControl } from '@eaccounting/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

export default function Introduction() {
	const { updateSettings } = useSettings();

	const handleSubmit = (form) => {
		const { financial_year_start } = form;
		updateSettings('financial_year_start', financial_year_start);
	};

	return (
		<>
			<h1>Company Setup</h1>
			<CompanySetupForm onSubmit={handleSubmit} />
		</>
	);
}

function CompanySetupForm(onSubmit) {
	const {
		company_name,
		financial_year_start,
		company_address,
		company_city,
		company_country,
	} = useSettings();

	return (
		<Form
			initialValues={{
				company_name,
				financial_year_start,
				company_address,
				company_city,
				company_country,
			}}
			onSubmitCallback={onSubmit}
		>
			{({ getInputProps, isValidForm, handleSubmit }) => (
				<>
					<div className="ea-row">
						<TextControl
							className="ea-col-6"
							label={__('Company Name')}
							{...getInputProps('company_name')}
						/>
						<DatePicker
							className="ea-col-6"
							label={__('Financial year start')}
							dateFormat={'MM-DD'}
							{...getInputProps('financial_year_start')}
						/>
						<TextControl
							className="ea-col-6"
							label={__('Street')}
							{...getInputProps('company_address')}
						/>
						<TextControl
							className="ea-col-6"
							label={__('City')}
							{...getInputProps('company_city')}
						/>
						<TextControl
							className="ea-col-6"
							label={__('Country')}
							{...getInputProps('company_country')}
						/>
					</div>
					<Button
						type="submit"
						isPrimary
						disabled={!isValidForm}
						onClick={handleSubmit}
					>
						{__('Submit')}
					</Button>
				</>
			)}
		</Form>
	);
}
