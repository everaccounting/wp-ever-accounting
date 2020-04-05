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
	AccountControl,
	CategoryControl,
	ContactControl,
	Button,
	Blocker,
	PaymentMethodControl,
	FileUpload
} from '@eaccounting/components';
import {withData} from '@eaccounting/hoc';
import {withSelect, withDispatch} from "@wordpress/data";
import {compose} from "@wordpress/compose";
import {Form, Field} from "react-final-form";
import {get} from "lodash";
import apiFetch from "@wordpress/api-fetch";
import {Redirect} from 'react-router-dom';
import './style.scss';
import {normalizeResponseBody} from "@eaccounting/helpers";

class EditPayment extends Component {
	constructor(props) {
		super(props);
		this.state = {
			changed: false
		}
	}

	onSubmit = async data => {
		console.log(data);
		// data.account_id = data.account && data.account.id && data.account.id;
		// data.currency = data.currency && data.currency.code && data.currency.code;
		// data.contact_id = data.contact && data.contact.id && data.contact.id;
		// data.category_id = data.category && data.category.id && data.category.id;
		// data.file_ids = data.files.map(file => file.id).join(',');
		// delete data.currency;
		// delete data.account;
		// delete data.contact;
		// delete data.category;
		//
		// await apiFetch({path: 'ea/v1/payments', method: 'POST', data}).then(res => {
		// 	this.props.replaceEntity('payments', res);
		// 	this.setState({
		// 		changed: true
		// 	})
		// }).catch(error => {
		// 	alert(error.message);
		// });
	};


	render() {
		const {payment, settings, match, isLoading} = this.props;
		const isAdd = get(match, ['params', 'id'], '') === 'add';
		var edit = {};


		return (
			<Fragment>
				{isAdd && <CompactCard tagName="h3">{__('Add Payment')}</CompactCard>}
				{!isAdd && <CompactCard tagName="h3">{__('Update Payment')}</CompactCard>}
				<Card>
					<Form
						onSubmit={this.onSubmit}
						initialValues={payment}
						render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
							<Blocker isBlocked={isLoading || submitting}>
								<form onSubmit={handleSubmit}>
									<div className="ea-row">
										<div className="ea-col-6">
											<Field
												label={__('Date', 'wp-ever-accounting')}
												name="paid_at"
												before={<Icon icon={'calendar'}/>}
												required>
												{props => (
													<DateControl {...props.input} {...props}/>
												)}
											</Field>
										</div>

										<div className="ea-col-6">
											<Field
												label={__('Account', 'wp-ever-accounting')}
												name="account"
												defaultValue={settings && settings.default_account && settings.default_account}
												before={<Icon icon={'university'}/>}
												required>
												{props => (
													<AccountControl {...props.input} {...props}/>
												)}
											</Field>
										</div>

										<div className="ea-col-6">
											<Field
												label={__('Amount', 'wp-ever-accounting')}
												name="amount"
												code={values.account && values.account.currency && values.account.currency.code || ''}
												before={<Icon icon={'money'}/>}
												required>
												{props => (
													<PriceControl {...props.input} {...props}/>
												)}
											</Field>
										</div>


										<div className="ea-col-6">
											<Field
												label={__('Category', 'wp-ever-accounting')}
												name="category"
												before={<Icon icon={'folder-open-o'}/>}
												required>
												{props => (
													<CategoryControl type="expense" {...props.input} {...props}/>
												)}
											</Field>
										</div>

										<div className="ea-col-6">
											<Field
												label={__('Vendor', 'wp-ever-accounting')}
												name="contact"
												before={<Icon icon={'user'}/>}>
												{props => (
													<ContactControl {...props.input} {...props}/>
												)}
											</Field>
										</div>

										<div className="ea-col-6">
											<Field
												label={__('Payment Method', 'wp-ever-accounting')}
												name="payment_method"
												defaultValue={settings && settings.default_payment_method && settings.default_payment_method}
												before={<Icon icon={'credit-card'}/>}
												required>
												{props => (
													<PaymentMethodControl {...props.input} {...props}/>
												)}
											</Field>
										</div>

										<div className="ea-col-12">
											<Field
												label={__('Description', 'wp-ever-accounting')}
												name="description">
												{props => (
													<TextareaControl {...props.input} {...props}/>
												)}
											</Field>
										</div>

										<div className="ea-col-6">
											<Field
												label={__('Reference', 'wp-ever-accounting')}
												name="reference"
												before={<Icon icon={'file-text-o'}/>}>
												{props => (
													<TextControl {...props.input} {...props}/>
												)}
											</Field>
										</div>

										<div className="ea-col-6">
											<Field
												name="files">
												{props => (
													<FileUpload {...props.input}/>
												)}
											</Field>

										</div>

									</div>

									<p>
										<Button isPrimary disabled={submitting || pristine}
												type="submit">{__('Submit')}</Button>
									</p>

								</form>
							</Blocker>
						)}
					/>

				</Card>
			</Fragment>
		);
	}
}


export default compose([
	withSelect((select, ownProps) => {
		const id = get(ownProps, ['match', 'params', 'id'], undefined);
		const {getEntityById, isRequestingGetEntityById} = select('ea/collection');
		const isNew = isNaN(id);
		return {
			payment: !isNew ? getEntityById('payments', id, {files: []}) : {files: []},
			isLoading: !isNew ? isRequestingGetEntityById('payments', id, {}) : false,
		}
	}),
	withDispatch(dispatch => {
		const {replaceEntity} = dispatch('ea/collection');
		return {
			replaceEntity
		}
	}),
])(EditPayment);
