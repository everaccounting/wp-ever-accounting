/**
 * External dependencies
 */
import { Input } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

const App = () => {
	return (
		<div className="eac-card">
			<div className="tw-grid tw-grid-cols-2 tw-gap-4">
				<div>Col 1</div>
				<div className="tw-grid tw-grid-cols-2 tw-gap-x-[15px]">
					<Input hideLabelFromVision label={ __( 'Issue Date' ) } name="issue_date" />
					<Input hideLabelFromVision label={ __( 'Bill Number' ) } name="bill_number" />
					<Input hideLabelFromVision label={ __( 'Due Date' ) } name="due_date" />
					<Input hideLabelFromVision label={ __( 'Order Number' ) } name="reference" />
					<Input.Autocomplete
						isMulti
						label={ __( 'Currency' ) }
						name="currency"
						defaultOptions
						hideLabelFromVision
						loadOptions={ ( inputValue ) => {
							return apiFetch( {
								path: `/eac/v1/currencies?search=${ inputValue }`,
							} );
						} }
						getOptionLabel={ ( option ) => option.name }
						getOptionValue={ ( option ) => option.code }
					/>
					<Input.Amount
						value="1000"
						label={ __( 'Exchange Rate' ) }
						name="exchange_rate"
					/>
					<Input.Amount label={ __( 'Total' ) } name="total" />
					<Input.Autocomplete
						isMulti
						label={ __( 'Taxes' ) }
						name="taxes"
						defaultOptions
						loadOptions={ ( inputValue ) =>
							apiFetch( { path: `/eac/v1/taxes?search=${ inputValue }` } )
						}
						getOptionLabel={ ( option ) => option.name }
						getOptionValue={ ( option ) => option.id }
					/>
				</div>
			</div>
		</div>
	);
};

export default App;
