import React, {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
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
	AccountControl,
	CategoryControl,
	ContactControl,
	Button,
	SelectControl,
	Row,
	Col
} from '@eaccounting/components';
import withModel from "../../with-model";

import apiFetch from '@wordpress/api-fetch';
class EditPayment extends Component {
	_isMounted = false;

	constructor(props) {
		super(props);
		this.state = {
			...props.model
		};
	}

	componentDidMount() {
		// this._isMounted = true;
		// const {match} = this.props;
		// const id = match.params.id || undefined;
		// id && this.loadPayment(id);
		// apiFetch({path:'/ea/v1/payments/', method:'OPTIONS'}).then(res=> {
		// 	console.log(res);
		// })
	}

	componentWillUnmount() {
		this._isMounted = false;
	}

	// loadPayment = id => {
	// 	apiRequest(accountingApi.revenues.get(id)).then(res => {
	// 		this._isMounted &&
	// 		this.setState({
	// 			...this.state,
	// 			...res.data,
	// 		});
	// 	});
	// };
	//
	addContactBtn = () => {
		return <Icon icon="plus"/>;
	};
	//
	// onSubmit = () => {
	// 	const {id, paid_at, amount, account, category, contact, reference, payment_method, description} = this.state;
	// 	const data = {
	// 		id,
	// 		paid_at,
	// 		amount,
	// 		account_id: account && account.id ? account.id : undefined,
	// 		category_id: category && category.id ? category.id : undefined,
	// 		contact_id: contact && contact.id ? contact.id : undefined,
	// 		reference,
	// 		payment_method,
	// 		description
	// 	};
	//
	// 	this._isMounted && this.setState({
	// 		isSaving: !this.state.isSaving
	// 	});
	//
	// 	let endpoint = accountingApi.payments.create(data);
	// 	if (id) {
	// 		endpoint = accountingApi.payments.update(id, data);
	// 	}
	//
	// 	this._isMounted && apiRequest(endpoint).then(res => {
	// 		this._isMounted && this.setState({
	// 			isSaving: !this.state.isSaving
	// 		});
	//
	// 	})
	//
	// };


	render() {
		const {id, paid_at, amount, account, category, contact, reference, description, isSaving, payment_method} = this.state;
		console.log(this.props.model);
		return (
			<Fragment>
				{!id && <CompactCard tagName="h3">{__('Add Payment')}</CompactCard>}
				{!!id && <CompactCard tagName="h3">{__('Update Payment')}</CompactCard>}
				<Card>
					<form onSubmit={this.onSubmit}>
						<Row>
							<Col>
								<DateControl
									label={__('Date')}
									before={<Icon icon={'calendar'}/>}
									value={paid_at}
									required
									onChange={paid_at => {
										{
											this.setState({paid_at});
										}
									}}
								/>
							</Col>
							<Col>
								<PriceControl
									label={__('Amount')}
									before={<Icon icon={'money'}/>}
									code={account && account.currency_code && account.currency_code}
									required
									value={amount}
									onChange={amount => {
										this.setState({amount})
									}}/>
							</Col>
							<Col>
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
							</Col>
							<Col>
								<CategoryControl
									label={__('Category')}
									before={<Icon icon={'folder-open-o'}/>}
									after={this.addContactBtn()}
									required
									type="expense"
									value={category}
									onChange={category => {
										this.setState({category});
									}}
								/>
							</Col>
							<Col>
								<ContactControl
									label={__('Customer')}
									before={<Icon icon={'user'}/>}
									type="vendor"
									value={contact}
									onChange={contact => this.setState({contact})}
								/>
							</Col>

							{/*<Col>*/}
							{/*	<Select*/}
							{/*		label={__('Payment Method')}*/}
							{/*		before={<Icon icon={'credit-card'}/>}*/}
							{/*		required*/}
							{/*		value={payment_method}*/}
							{/*		options={Object.keys(eAccountingi10n.data.paymentMethods).map(key => {*/}
							{/*			return {value: key, label: eAccountingi10n.data.paymentMethods[key]};*/}
							{/*		})}*/}
							{/*		onChange={payment_method => this.setState({payment_method})}*/}
							{/*	/>*/}
							{/*</Col>*/}
							{/*<Col>*/}
							{/*	<TextControl*/}
							{/*		label={__('Reference')}*/}
							{/*		before={<Icon icon={'file-text-o'}/>}*/}
							{/*		value={reference}*/}
							{/*		onChange={reference => this.setState({reference})}*/}
							{/*	/>*/}
							{/*</Col>*/}
							{/*<Col>*/}
							{/*	<TextareaControl*/}
							{/*		label={__('Description')}*/}
							{/*		value={description}*/}
							{/*		onChange={description => this.setState({description})}*/}
							{/*	/>*/}
							{/*</Col>*/}
						</Row>

						<Button isPrimary isBusy={isSaving} onClick={this.onSubmit}>
							{__('Submit')}
						</Button>

					</form>
				</Card>
			</Fragment>
		);
	}
}

export default withModel('payments')(EditPayment)
