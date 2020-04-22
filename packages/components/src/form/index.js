import {Component} from '@wordpress/element';
import {Form as FinalForm} from "react-final-form";
import PropTypes from 'prop-types';

export default class Form extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const {className, onSubmit, onProcess= (x) => x} = this;

		const childrenWithProps = React.Children.map(this.props.children, child =>
			React.cloneElement(child, { submitting, pristine, values })
		);

		return (
			<FinalForm
				onSubmit={onSubmit}
				initialValues={{}}
				render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
					<form onSubmit={handleSubmit} className={className}>
						{childrenWithProps}
					</form>
				)}
			/>
		)
	}
}

Form.prototypes = {
	onSubmit:PropTypes.func.isRequired,
	onProcess:PropTypes.func
};
