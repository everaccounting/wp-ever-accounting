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
	Spinner,
	Select,
	Button, SelectControl, Navigation,
} from '@eaccounting/components';
import AccountControl from '../account-control';
import {accountingApi, apiRequest} from "../../lib/api";

export default class EditTransfer extends Component {
	_isMounted = false;

	constructor(props) {
		super(props);
		this.state = {
			date: '',
			from_account: '',
			to_account: '',
			amount: '',
			isSaving: false,
		};
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	componentDidMount() {
		this._isMounted = true;
		const {match} = this.props;
		const id = match.params.id || undefined;
		id && this.loadTransfer(id);
	}

	componentWillUnmount() {
		this._isMounted = false;
	}


	loadTransfer = id => {
		apiRequest(accountingApi.transfers.get(id)).then(res => {
			this._isMounted &&
			this.setState({
				...this.state,
				...res.data,
			});
		});
	};

	render() {
		const {id, from_account, to_account, date, amount, description, payment_method, reference, isSaving} = this.state;

		return (
			<Fragment>
				{!id && <CompactCard tagName="h3">{__('Add Transfer')}</CompactCard>}
				{!!id && <CompactCard tagName="h3">{__('Update Transfer')}</CompactCard>}
				<Card>
					<form onSubmit={this.onSubmit}>
						<div className="ea-row">

							<div className="ea-col-6">
								<AccountControl
									label={__('From Account')}
									before={<Icon icon={'university'}/>}
									after={from_account && from_account.currency_code && from_account.currency_code}
									required
									value={from_account && from_account}
									onChange={from_account => {
										this.setState({from_account});
									}}
								/>
							</div>

							<div className="ea-col-6">
								<AccountControl
									label={__('To Account')}
									before={<Icon icon={'university'}/>}
									isOptionDisabled={option => from_account && from_account.id && parseInt(from_account.id, 10) === parseInt(option.id, 10)}
									after={to_account && to_account.currency_code && to_account.currency_code}
									required
									value={to_account && to_account}
									onChange={to_account => {
										this.setState({to_account});
									}}
								/>
							</div>

							<div className="ea-col-6">
								<PriceControl
									label={__('Amount')}
									before={<Icon icon={'university'}/>}
									code={from_account && from_account.currency && from_account.currency.code && from_account.currency.code}
									required
									value={amount}
									onChange={amount => {
										this.setState({amount});
									}}
								/>
							</div>


							<div className="ea-col-6">
								<DateControl
									label={__('Date')}
									before={<Icon icon={'calendar'}/>}
									value={date}
									required
									onChange={date => {
										this.setState({date});
									}}
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
					</form>

				</Card>
			</Fragment>
		);
	}
}
