import {Field as Base} from "react-final-form";
import {Component, Fragment} from "@wordpress/element";
import PropTypes from 'prop-types';


export default class Field extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const {col, field, ...rest} = this.props;
		return (
			<Fragment>
				<Base {...this.props}/>
			</Fragment>
		)
	}
}
Field.propTypes = {
	name: PropTypes.string.isRequired,
	label: PropTypes.string.isRequired,
	col: PropTypes.bool,
};
