import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {
	AccountControl,
	CategoryControl,
	CategoryTypesControl,
	ContactControl,
	ContactTypesControl,
	CountryControl,
	CurrencyControl,
	DateControl,
	PriceControl,
	PaymentMethodControl

} from "@eaccounting/components";

export default class Dashboard extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	onChange = (value) =>{
		console.log(value);
	};

	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Dashboard')}</h1>
				Account
				<AccountControl
				onChange={this.onChange}/>
				Category
				<CategoryControl
					onChange={this.onChange}/>
					Category type
				<CategoryTypesControl
					onChange={this.onChange}/>
					Contact
				<ContactControl
					onChange={this.onChange}/>
					contact type
				<ContactTypesControl
					onChange={this.onChange}/>
					Country
				<CountryControl
					onChange={this.onChange}/>
					Currency
				<CurrencyControl
					value={2}
					onChange={this.onChange}/>
					Date
				<DateControl
					onChange={this.onChange}/>
					Price
				<PriceControl
					onChange={this.onChange}/>
					Payment method
				<PaymentMethodControl
					onChange={this.onChange}/>
			</Fragment>
		);
	}
}

