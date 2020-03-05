import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {
	Card,
	CompactCard,
	Icon,
	TextareaControl,
	TextControl,
	DateControl,
	PriceControl,
	Spinner, Button
} from "@eaccounting/components";
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
			contact: {
				first_name:'',
				last_name:'',
			},
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

	addContactBtn = () => {
		return (<Icon icon="plus"/>)
	};

	render() {
		const {id, paid_at, amount, account, category, contact, reference, description} = this.state;
		const {currency_code} = account;
		return (
			<Fragment>

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
							<PriceControl
								label={__('Amount')}
								before={<Icon icon={'university'}/>}
								code={currency_code}
								required
								value={amount}
								onChange={(amount) => {
									this.setState({amount})
								}}/>
						</div>

						<div className="ea-col-6">
							<AccountControl
								label={__('Account')}
								before={<Icon icon={'university'}/>}
								after={currency_code}
								required
								value={account}
								onChange={(account) => {
									this.setState({account})
								}}/>
						</div>

						<div className="ea-col-6">
							<CategoryControl
								label={__('Category')}
								before={<Icon icon={'folder-open-o'}/>}
								required
								type="income"
								value={category}
								onChange={(category) => {
									this.setState({category})
								}}/>
						</div>

						<div className="ea-col-6">
							<ContactControl
								label={__('Customer')}
								before={<Icon icon={'user'}/>}
								after={this.addContactBtn()}
								type="customer"
								value={contact}
								onChange={(contact) => this.setState({contact})}/>
						</div>


						<div className="ea-col-6">
							<TextControl
								label={__('Reference')}
								before={<Icon icon={'file-text-o'}/>}
								value={reference}
								onChange={(reference) => this.setState({reference})}/>
						</div>

						<div className="ea-col-12">
							<TextareaControl
								label={__('Description')}
								value={description}
								onChange={(description) => this.setState({description})}/>
						</div>


					</div>

				</Card>
			</Fragment>
		)
	}
}
