import {Component} from '@wordpress/element';
import Modal from "../modal";
import {Form, Field} from "react-final-form";
import PropTypes from "prop-types";
import TextControl from "../text-control";
import CurrencySelect from "../currency-select";
import TextAreaControl from "../textarea-control";
import CountrySelect from "../country-select";
import {__} from '@wordpress/i18n';
import Button from "../button";
import {get, pickBy, isObject} from "lodash";

const processFormData = (data) => (pickBy({
	...data,
	currency_code: get(data, 'currency.code')
}, value => !isObject(value)));


class Create extends Component {
	render() {
		return (
			<Modal title={__('Add New')} onClose={this.props.onClose}>
				<Form
					onSubmit={(data) => this.props.onSubmit(processFormData(data))}
					initialValues={{}}
					render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
						<form onSubmit={handleSubmit} className="ea-row">

							<Field
								label={__('Name', 'wp-ever-accounting')}
								name="name"
								className="ea-col-6"
								required>
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Currency', 'wp-ever-accounting')}
								name="currency"
								className="ea-col-6"
								required>
								{props => (
									<CurrencySelect {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Email', 'wp-ever-accounting')}
								name="email"
								className="ea-col-6"
								required>
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Country', 'wp-ever-accounting')}
								name="country"
								className="ea-col-6">
								{props => (
									<CountrySelect {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Address', 'wp-ever-accounting')}
								className="ea-col-12"
								name="address">
								{props => (
									<TextAreaControl {...props.input} {...props}/>
								)}
							</Field>

							<p className="ea-col-12">
								<Button
									isPrimary
									disabled={submitting || pristine}
									type="submit">{__('Submit')}
								</Button>
							</p>

						</form>
					)}/>
			</Modal>
		)
	}
}

Create.propTypes = {
	onClose: PropTypes.func.isRequired,
	onSubmit: PropTypes.func.isRequired,
};
export default Create;
