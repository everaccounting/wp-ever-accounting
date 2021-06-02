/**
 * External dependencies
 */
import { useEntity, useSettings } from '@eaccounting/data';
import { Table, Loading } from '@eaccounting/components';
/**
 * WordPress dependencies
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const CurrencyRate = (currency) => {
	return currency.rate;
};

export default function Introduction() {
	const { entities, isLoading } = useEntity({ name: 'currencies' });
	// const { default_currency } = useSettings();

	const setDefaultCurrency = (e) => {};

	return (
		<>
			<h1>{__('Currency Setup updated')}</h1>
			<p>
				{__(
					'Default currency rate should be always 1 & additional currency rates should be equivalent of default currency. e.g. If USD is your default currency then USD rate is 1 & GBP rate will be 0.77',
					'wp-ever-accounting'
				)}
			</p>
			<Loading loading={isLoading}>
				<Table
					columns={[
						{
							label: __('Name'),
							property: 'name',
						},
						{
							label: __('Code'),
							property: 'code',
						},
						{
							label: __('Rate'),
							property: 'rate',
							render: CurrencyRate,
						},
						{
							label: __('Default'),
							render: (row) => {
								return (
									<input
										type="radio"
										name="default_currency"
										checked={true}
										value={row.code}
										onChange={setDefaultCurrency}
									/>
								);
							},
						},
					]}
					data={entities}
				/>
			</Loading>
		</>
	);
}
