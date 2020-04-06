import {Component, Fragment} from '@wordpress/element';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {Form, Field} from "react-final-form";
import {TextControl, Icon, Modal, Button, CategoryTypesControl} from "@eaccounting/components";


export default class EditCategory extends Component {
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
		const {defaultCategory='income'} = this.props;
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
								label={__('Category Type', 'wp-ever-accounting')}
								name="type"
								defaultValue={defaultCategory}
								before={<Icon icon={'bars'}/>}
								required>
								{props => (
									<CategoryTypesControl {...props.input} {...props}/>
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
