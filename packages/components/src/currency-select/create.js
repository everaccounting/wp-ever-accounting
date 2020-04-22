/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
/**
 * Internal dependencies
 */
import Modal from '../modal';
/**
 * External dependencies
 */
import { Form, Field } from 'react-final-form';
import PropTypes from 'prop-types';
import TextControl from '../text-control';
import { __ } from '@wordpress/i18n';
import Button from '../button';
import SelectControl from '../select-control';
import { getGlobalCurrencies } from '@eaccounting/data';

export default class Create extends Component {
	render() {
		const currencies = getGlobalCurrencies();
		return (
			<Modal title={__('New Currency')} onClose={this.props.onClose}>
				<Form
					onSubmit={this.props.onSubmit}
					initialValues={{}}
					render={({ handleSubmit, submitting, pristine, values }) => (
						<form onSubmit={handleSubmit} className="ea-row">
							<Field label={__('Name', 'wp-ever-accounting')} name="name" className="ea-col-12" required>
								{props => <TextControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Code', 'wp-ever-accounting')}
								name="code"
								className="ea-col-12"
								options={currencies}
								required
							>
								{props => <SelectControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Rate', 'wp-ever-accounting')}
								name="rate"
								className="ea-col-12"
								defaultValue={1}
								parse={value => value.replace(/[^\d.]+/g, '')}
								help={__('Rate against default currency. NOTE: Default currency rate is always 1')}
								required
							>
								{props => <TextControl {...props.input} {...props} />}
							</Field>

							<p className="ea-col-12">
								<Button isPrimary disabled={submitting || pristine} type="submit">
									{__('Submit')}
								</Button>
							</p>
						</form>
					)}
				/>
			</Modal>
		);
	}
}

Create.propTypes = {
	onClose: PropTypes.func.isRequired,
	onSubmit: PropTypes.func.isRequired,
};
