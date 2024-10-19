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
					<Input label={ __( 'Issue Date' ) } name="issue_date" />
					<Input label={ __( 'Bill Number' ) } name="bill_number" />
					<Input label={ __( 'Due Date' ) } name="due_date" />
					<Input label={ __( 'Order Number' ) } name="reference" />
					<Input.Autocomplete
						label={ __( 'Currency' ) }
						name="currency"
						defaultOptions
						loadOptions={ ( inputValue ) => {
							return apiFetch( {
								path: `/eac/v1/currencies?search=${ inputValue }`,
							} );
						} }
						getOptionLabel={ ( option ) => option.name }
					/>
					<Input label={ __( 'Exchange Rate' ) } name="exchange_rate" />
					<Input.Amount label={ __( 'Total' ) } name="total" />
					<Input.Autocomplete
						label={ __( 'Taxes' ) }
						name="taxes"
						defaultOptions
						loadOptions={ ( inputValue ) =>
							apiFetch( { path: `/eac/v1/taxes?search=${ inputValue }` } )
						}
						getOptionLabel={ ( option ) => option.name }
					/>
				</div>
			</div>
		</div>
	);
};

export default App;
