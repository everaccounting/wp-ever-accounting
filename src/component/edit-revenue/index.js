import React, {Component, Fragment} from 'react';
import {translate as __} from 'lib/locale';
import {
	Card,
	CompactCard,
	Icon,
	TextareaControl,
	TextControl,
	DateControl,
	PriceControl,
	Select,
	Button,
} from '@eaccounting/components';
import AccountControl from '../account-control';
import CategoryControl from '../category-control';
import ContactControl from '../contact-control';
import {accountingApi, apiRequest} from '../../lib/api';

export default class EditRevenue extends Component {
	_isMounted = false;

	constructor(props) {
		super(props);
		this.state = {
			id: null,
			paid_at: '',
			account: eAccountingi10n.data.account || {},
			amount: 0,
			contact: {
				first_name: '',
				last_name: '',
			},
			description: '',
			category: {},
			reference: '',
			payment_method: 'cash',
			attachment_url: '',
			parent_id: '',
			reconciled: '0',
			isSaving: false,
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

	loadRevenue = id => {
		apiRequest(accountingApi.revenues.get(id)).then(res => {
			this._isMounted &&
			this.setState({
				...this.state,
				...res.data,
			});
		});
	};

	addContactBtn = () => {
		return <Icon icon="plus"/>;
	};

	onSubmit = () => {
		const {id, paid_at, amount, account, category, contact, reference, payment_method, description} = this.state;
		const data = {
			id,
			paid_at,
			amount,
			account_id: account && account.id && account.id,
			category_id: category && category.id && category.id,
			contact_id: contact && contact.id && contact.id,
			reference,
			payment_method,
			description
		};

		this._isMounted && this.setState({
			isSaving: !this.state.isSaving
		});

		let endpoint = accountingApi.revenues.create(data);
		if (id) {
			endpoint = accountingApi.revenues.update(id, data);
		}

		this._isMounted && apiRequest(endpoint).then(res => {
			console.log(res);
			this._isMounted && this.setState({
				isSaving: !this.state.isSaving
			});

		})

	};


	render() {
		const {id, paid_at, amount, account, category, contact, reference, description, isSaving, payment_method} = this.state;
		return (
			<Fragment>
				{!id && <CompactCard tagName="h3">{__('Add Revenue')}</CompactCard>}
				{!!id && <CompactCard tagName="h3">{__('Update Revenue')}</CompactCard>}
				<Card>
					<form onSubmit={this.onSubmit}>
						<div className="ea-row">
							<div className="ea-col-6">
								<DateControl
									label={__('Date')}
									before={<Icon icon={'calendar'}/>}
									value={paid_at}
									required
									onChange={paid_at => {
										this.setState({paid_at});
									}}
								/>
							</div>

							<div className="ea-col-6">
								<PriceControl
									label={__('Amount')}
									before={<Icon icon={'university'}/>}
									code={account && account.currency_code && account.currency_code}
									required
									value={amount}
									onChange={amount => {
										this.setState({amount});
									}}
								/>
							</div>

							<div className="ea-col-6">
								<AccountControl
									label={__('Account')}
									before={<Icon icon={'university'}/>}
									after={account && account.currency_code && account.currency_code}
									required
									value={account}
									onChange={account => {
										this.setState({account});
									}}
								/>
							</div>

							<div className="ea-col-6">
								<CategoryControl
									label={__('Category')}
									before={<Icon icon={'folder-open-o'}/>}
									after={this.addContactBtn()}
									required
									type="income"
									value={category}
									onChange={category => {
										this.setState({category});
									}}
								/>
							</div>

							<div className="ea-col-6">
								<ContactControl
									label={__('Customer')}
									before={<Icon icon={'user'}/>}
									after={this.addContactBtn()}
									type="customer"
									value={contact}
									onChange={contact => this.setState({contact})}
								/>
							</div>

							<div className="ea-col-6">
								<Select
									label={__('Payment Method')}
									before={<Icon icon={'credit-card'}/>}
									required
									value={payment_method}
									options={Object.keys(eAccountingi10n.data.paymentMethods).map(key => {
										return {value: key, label: eAccountingi10n.data.paymentMethods[key]};
									})}
									onChange={payment_method => this.setState({payment_method})}
								/>
							</div>

							<div className="ea-col-6">
								<TextControl
									label={__('Reference')}
									before={<Icon icon={'file-text-o'}/>}
									value={reference}
									onChange={reference => this.setState({reference})}
								/>
							</div>

							<div className="ea-col-12">
								<TextareaControl
									label={__('Description')}
									value={description}
									onChange={description => this.setState({description})}
								/>
							</div>
						</div>

						<Button isPrimary isBusy={isSaving} onClick={this.onSubmit}>
							{__('Submit')}
						</Button>

					</form>
				</Card>
			</Fragment>
		);
	}
}
