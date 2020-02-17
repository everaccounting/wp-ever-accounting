/**
 * External dependencies
 */

import {Component, Fragment} from 'react';
import {translate as __, numberFormat} from 'lib/locale';
import {connect} from 'react-redux';
import classnames from 'classnames';
import PropTypes from 'prop-types';
import Highlighter from 'react-highlight-words';
import {Modal, Form} from '@eaccounting/components';
import {Button, TextControl} from '@wordpress/components';
import CurrencyInput from 'react-currency-input';

/**
 * Internal dependencies
 */
import {setSelected, performTableAction, updateAccount} from 'state/accounts/action';
import {STATUS_SAVING, STATUS_IN_PROGRESS} from 'lib/status';
import Spinner from 'component/spinner';
import Column from 'component/table/column';
import RowActions from 'component/table/row-action';
import {eAccountingApi, getApi} from "../../lib/api";
import {getApiRequest} from 'lib/api';
class AccountsRow extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		selected: PropTypes.bool.isRequired,
		status: PropTypes.string.isRequired,
		defaultFlags: PropTypes.object,
	};

	constructor(props) {
		super(props);

		this.state = {
			editing: false,
			name: props.item.name,
			currencies:[],
		};
	}

	onEdit = ev => {
		ev.preventDefault();
		this.setState({editing: !this.state.editing});
		getApi(eAccountingApi.currencies.list()).then(json => {
			console.log(json);
		});
	};

	onDelete = ev => {
		ev.preventDefault();
		this.props.onTableAction('delete', this.props.item.id);
	};

	onDisable = ev => {
		ev.preventDefault();
		this.props.onTableAction('disable', this.props.item.id);
	};

	onEnable = ev => {
		ev.preventDefault();
		this.props.onTableAction('enable', this.props.item.id);
	};

	onSelected = () => {
		this.props.onSetSelected([this.props.item.id]);
	};

	onValidate = (values) => {
		const errors = {};

		// if (values.company.length < 3) {
		// 	errors.company = __('Company Name is required', 'wp-ever-crm');
		// }
		// if (values.email.length < 3) {
		// 	errors.email = __('Email is required', 'wp-ever-crm');
		// }
		return errors;
	};

	onSubmit = item => {
		this.props.onSaveAccount(this.props.item.id, item);
		this.onClose();
	};

	onClose = () => {
		this.setState({editing: !this.state.editing});
	};

	getActions() {
		const {id, enabled} = this.props.item;
		const actions = [];
		actions.push([__('Edit'), this.onEdit]);
		actions.push([__('Delete'), this.onDelete]);
		if (enabled) {
			actions.push([__('Disable'), this.onDisable]);
		} else {
			actions.push([__('Enable'), this.onEnable]);
		}

		return actions
			.map((item, pos) => <a key={pos} href={item[2] ? item[2] : '#'} onClick={item[1]}>{item[0]}</a>)
			.reduce((prev, curr) => [prev, ' | ', curr]);
	}

	renderActions(saving) {
		return (
			<RowActions disabled={saving}>
				{this.getActions()}
			</RowActions>
		);
	}

	render() {
		const {id, name, balance, number, bank_name, enabled} = this.props.item;
		const {selected, status, currentDisplaySelected} = this.props;
		const isLoading = status === STATUS_IN_PROGRESS;
		const isSaving = status === STATUS_SAVING;
		const hideRow = !enabled || isLoading || isSaving;

		return (
			<tr className={hideRow ? 'disabled' : ''}>

				<th scope="row" className="check-column">
					{!isSaving &&
					<input type="checkbox" name="item[]" value={id} disabled={isLoading} checked={selected}
						   onChange={this.onSelected}/>}
					{isSaving && <Spinner size="small"/>}
				</th>

				<Column enabled="name" className="column-primary column-name" selected={currentDisplaySelected}>
					{name}
					{this.renderActions(isSaving)}
				</Column>

				<Column enabled="balance" className="column-balance" selected={currentDisplaySelected}>
					<span className='ea-money'>{balance}</span>
				</Column>

				<Column enabled="number" className="column-number" selected={currentDisplaySelected}>
					{number}
				</Column>

				<Column enabled="bank_name" className="column-bank-name" selected={currentDisplaySelected}>
					{bank_name}
				</Column>

				<Column enabled="status" className="column-status" selected={currentDisplaySelected}>
					{enabled ? <span className='ea-item-status enabled'>{__('Enabled')}</span> :
						<span className='ea-item-status disabled'>{__('Disabled')}</span>}
				</Column>

				{this.state.editing && <Modal title={__('Edit')} onRequestClose={this.onClose}>
					<Form validate={this.onValidate} onSubmitCallback={this.onSubmit} initialValues={this.props.item}>
						{({getInputProps, values, errors, handleSubmit}) => (
							<Fragment>
								<TextControl label={__('Name')} {...getInputProps('name')} required/>
								<TextControl label={__('Opening Balance')} {...getInputProps('opening_balance')} required/>
								<TextControl label={__('Account Number')} {...getInputProps('number')} required/>
								<TextControl label={__('Bank Name')} {...getInputProps('bank_name')} required/>
								<TextControl label={__('Bank Phone')} {...getInputProps('bank_phone')} required/>
								<CurrencyInput decimalSeparator="," thousandSeparator="." />
								<Button isPrimary isBusy={isSaving} onClick={handleSubmit}
										disabled={Object.keys(errors).length}>
									{__('Submit')}
								</Button>
							</Fragment>
						)}
					</Form>
				</Modal>}
			</tr>
		)
	}
}


function mapDispatchToProps(dispatch) {
	return {
		onSetSelected: items => {
			dispatch(setSelected(items));
		},
		onSaveAccount:(id, item) =>{
			dispatch(updateAccount(id, item));
		},
		onTableAction: (action, ids) => {
			dispatch(performTableAction(action, ids));
		},
	};
}

function mapStateToProps(state) {
	const {accounts} = state;

	return {
		accounts,
	};
}

export default connect(
	mapStateToProps,
	mapDispatchToProps,
)(AccountsRow);
