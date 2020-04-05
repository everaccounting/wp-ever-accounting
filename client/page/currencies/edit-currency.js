import {Component, Fragment} from '@wordpress/element';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {Form, Field} from "react-final-form";
import {TextControl, Icon, Modal, Button, SelectControl} from "@eaccounting/components";
import {getGlobalCurrencies} from "@eaccounting/data";

export default class EditCurrency extends Component {
	static propTypes = {
		item: PropTypes.object,
		onSubmit: PropTypes.func.isRequired,
		onClose: PropTypes.func.isRequired,
		tittle: PropTypes.string.isRequired,
		buttonTittle: PropTypes.string.isRequired,
	};

	static defaultProps = {
		item: {},
	};

	render() {
		const currencies = getGlobalCurrencies();
		return (
			<Modal title={this.props.tittle} onRequestClose={this.props.onClose}>
				<Form
					onSubmit={this.props.onSubmit}
					initialValues={this.props.item}
					render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
						<form onSubmit={handleSubmit}>
							<Field
								label={__('Name', 'wp-ever-accounting')}
								name="name"
								before={<Icon icon={'id-card-o'}/>}
								required>
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Code', 'wp-ever-accounting')}
								name="code"
								defaultValue={'USD'}
								options={currencies}
								before={<Icon icon={'code'}/>}
								required>
								{props => (
									<SelectControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Rate', 'wp-ever-accounting')}
								name="rate"
								defaultValue={1}
								before={<Icon icon={'money'}/>}
								parse={value => value.replace(/[^\d.]+/g, '')}
								help={__('Rate against default currency. NOTE: Default currency rate is always 1')}
								required>
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<p style={{marginTop: '20px'}}>
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
