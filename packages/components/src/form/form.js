import {Form as FinalForm} from "react-final-form";
import {Component, Fragment} from "@wordpress/element";
import PropTypes from 'prop-types';

export default class Form extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const {initialValues, onSubmit} = this.props;
		return (
			<Fragment>
				<FinalForm onSubmit={onSubmit} initialValues={initialValues}>
					{props => (
						<form onSubmit={props.handleSubmit}>
							{this.props.children && this.props.children}
						</form>
					)}
				</FinalForm>
			</Fragment>
		)
	}
}


Form.propTypes = {
	children: PropTypes.any,
	errors: PropTypes.object,
	initialValues: PropTypes.object,
	onSubmit: PropTypes.func,
	validate: PropTypes.func,
};


