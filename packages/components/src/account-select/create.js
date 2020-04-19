import {Component} from '@wordpress/element';
import Modal from "../modal";
import {Form, Field} from "react-final-form";
import PropTypes from "prop-types";
import TextControl from "../text-control";
import CurrencySelect from "../currency-select";
import {__} from '@wordpress/i18n';
import Button from "../button";
import PriceControl from "../price-control";
import {get, pickBy, isObject} from "lodash";

const processFormData = (data) => (pickBy({
	...data,
	currency_code: get(data, 'currency.code')
}, value => !isObject(value)));


class Create extends Component {
	render() {
		return (
			<Modal title={__('New Account')} onClose={this.props.onClose}>
				<Form
					onSubmit={(data) => this.props.onSubmit(processFormData(data))}
					initialValues={{}}
					render={({handleSubmit, submitting, pristine, values}) => (
						<form onSubmit={handleSubmit} className="ea-row">

							<Field
								label={__('Account Name', 'wp-ever-accounting')}
								name="name"
								className="ea-col-6"
								required>
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Account Number', 'wp-ever-accounting')}
								name="number"
								className="ea-col-6"
								required>
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Account Currency', 'wp-ever-accounting')}
								name="currency"
								className="ea-col-6"
								required>
								{props => (
									<CurrencySelect {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Opening Balance', 'wp-ever-accounting')}
								name="opening_balance"
								className="ea-col-6"
								code={get(values, 'currency.code')}
								required>
								{props => (
									<PriceControl {...props.input} {...props}/>
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
