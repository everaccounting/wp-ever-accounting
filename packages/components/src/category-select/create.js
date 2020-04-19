import {Component} from '@wordpress/element';
import Modal from "../modal";
import {Form, Field} from "react-final-form";
import PropTypes from "prop-types";
import TextControl from "../text-control";
import {__} from '@wordpress/i18n';
import Button from "../button";

class Create extends Component {
	render() {
		return (
			<Modal title={__('New Category')} onClose={this.props.onClose}>
				<Form
					onSubmit={this.props.onSubmit}
					initialValues={{}}
					render={({handleSubmit, submitting, pristine, values}) => (
						<form onSubmit={handleSubmit} className="ea-row">

							<Field
								label={__('Category Name', 'wp-ever-accounting')}
								name="name"
								className="ea-col-12"
								required>
								{props => (
									<TextControl {...props.input} {...props}/>
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
