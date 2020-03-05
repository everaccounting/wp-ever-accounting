import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {
	Card,
	CompactCard,
	Icon,
	TextareaControl,
	TextControl,
	DateControl,
	Spinner
} from "@eaccounting/components";
import PriceControl from "./price-control";
import AccountControl from "../account-control";
import CategoryControl from "../category-control";
import ContactControl from "../contact-control";
import {accountingApi, apiRequest} from "../../lib/api";

export default class EditRevenue extends Component {
	_isMounted = false;

	constructor(props) {
		super(props);
		this.state = {
			id: null,
			paid_at: '',
			account: {},
			amount: '',
			contact: {},
			description: '',
			category: {},
			reference: '',
			payment_method: '',
			attachment_url: '',
			parent_id: '',
			reconciled: '0',
		};
	}

	componentDidMount() {
		this._isMounted = true;
		const {match} = this.props;
		const id = match.params.id || undefined;
		id && this.loadRevenue(id);
	}

	componentWillUnmount() {
		this._isMounted = false;
	}

	loadRevenue = (id) => {
		apiRequest(accountingApi.revenues.get(id)).then(res => {
			this._isMounted && this.setState({
				...this.state,
				...res.data
			})
		});
	};


	render() {
		const {id, paid_at, amount, account} = this.state;
		const accountOption = {label: account.name, value: account};
		const {currency = {}, currency_code} = account;
		return (
			<Fragment>
				{currency_code}
				{!id && <CompactCard tagName="h3">{__('Add Revenue')}</CompactCard>}
				{!!id && <CompactCard tagName="h3">{__('Update Revenue')}</CompactCard>}
				<Card>

					<div className="ea-row">

						<div className="ea-col-6">
							<DateControl
								label={__('Date')}
								before={<Icon icon={'calendar'}/>}
								value={paid_at}
								required
								onChange={(paid_at) => {
									this.setState({paid_at})
								}}/>
						</div>

						<div className="ea-col-6">
							<PriceControl/>
							{/*<PriceControl*/}
							{/*	label={__('Amount')}*/}
							{/*	before={<Icon icon={'university'}/>}*/}
							{/*	currency={currency}*/}
							{/*	required*/}
							{/*	value={amount}*/}
							{/*	onChange={(amount) => {*/}
							{/*		this.setState({amount})*/}
							{/*	}}/>*/}
						</div>

						<div className="ea-col-6">
							<AccountControl
								label={__('Account')}
								before={<Icon icon={'university'}/>}
								after={currency_code}
								required
								value={{label:account.name || '', value:account}}
								onChange={(account) => {
									console.log(account);
									this.setState({account: account.value})
								}}/>
						</div>


					</div>

				</Card>
			</Fragment>
		)
	}
}
