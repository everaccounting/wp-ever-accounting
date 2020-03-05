import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import AccountControl from 'component/account-control';
import CategoryControl from 'component/category-control';
import ContactControl from 'component/contact-control';
import CurrencyControl from 'component/currency-control';
import Currency from '../../lib/currency';
import { TextControl, PriceControl } from '@eaccounting/components';

export default class Dashboard extends Component {
	constructor(props) {
		super(props);
		this.state = {
			code: 'USD',
		};
	}

	render() {
		const { code } = this.state;
		const storeCurrency = new Currency(code);

		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Dashboard')}</h1>
				{storeCurrency.formatCurrency(2000000000000000.123)}

				<PriceControl value={2000000000000000.123} after={code} code={code} />

				<AccountControl
					isMulti
					isClearable
					onChange={account => {
						console.log(account);
					}}
				/>
				<CategoryControl
					isMulti
					isClearable
					onChange={category => {

					}}
				/>
				<ContactControl
					isMulti
					isClearable
					onChange={category => {

					}}
				/>
				<CurrencyControl
					isClearable
					onChange={currency => {
						this.setState({
							code: currency.code,
						});
					}}
				/>
				<CategoryControl
					isClearable
					onChange={category => {

					}}
				/>
			</Fragment>
		);
	}
}
