/**
 * External dependencies
 */

import {Component, Fragment} from 'react';
import { translate as __ } from 'lib/locale';
import {Modal, Form} from '@eaccounting/components';

/**
 * Internal dependencies
 */
import './style.scss';
import {Button, TextControl} from "@wordpress/components";
import CurrencyInput from "react-currency-input";

export default class AddAccount extends Component {
	constructor( props ) {
		super(props);
		this.state = {};
		window.addEventListener( 'popstate', this.onPageChanged );
	}

	componentDidCatch( error, info ) {
		this.setState( { error: true, stack: error, info } );
	}

	componentWillUnmount() {
		window.removeEventListener( 'popstate', this.onPageChanged );
	}

	onClose = () => {

	}

	onSubmit = item => {
		this.props.onSaveAccount(this.props.item.id, item);
		this.onClose();
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

	render() {
		return(
			<Modal title={__('Add Account')} onRequestClose={this.onClose}>
				<Form validate={this.onValidate} onSubmitCallback={this.onSubmit} initialValues={this.props.item}>
					{({getInputProps, values, errors, handleSubmit}) => (
						<Fragment>
							<TextControl label={__('Name')} {...getInputProps('name')} required/>
							<TextControl label={__('Opening Balance')} {...getInputProps('opening_balance')} required/>
							<TextControl label={__('Account Number')} {...getInputProps('number')} required/>
							<TextControl label={__('Bank Name')} {...getInputProps('bank_name')} required/>
							<TextControl label={__('Bank Phone')} {...getInputProps('bank_phone')} required/>
							<CurrencyInput label={__('Opening Balance')} {...getInputProps('opening_balance')} required/>
							<Button isPrimary isBusy={isSaving} onClick={handleSubmit}
									disabled={Object.keys(errors).length}>
								{__('Submit')}
							</Button>
						</Fragment>
					)}
				</Form>
			</Modal>
		)
	}
}

// function mapDispatchToProps( dispatch ) {
// 	return {}
// }
// function mapStateToProps( state ) {
// 	return {}
// }
//
// export default connect(
// 	mapStateToProps,
// 	mapDispatchToProps,
// )( Account );
