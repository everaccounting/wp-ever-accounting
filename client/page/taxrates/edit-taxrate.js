import {Component, Fragment} from '@wordpress/element';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {Form, Field} from "react-final-form";
import {TextControl, Icon, Modal, Button, SelectControl} from "@eaccounting/components";
import {TAX_RATE_TYPES} from "@eaccounting/data"
export default class EditTaxRate extends Component {
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
								label={__('Rate', 'wp-ever-accounting')}
								name="rate"
								parse={value => value.replace(/[^\d.]+/g, '')}
								before={<Icon icon={'money'}/>}
								required>
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Type', 'wp-ever-accounting')}
								name="type"
								options={TAX_RATE_TYPES}
								before={<Icon icon={'bars'}/>}
								required>
								{props => (
									<SelectControl {...props.input} {...props}/>
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