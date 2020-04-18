import {Component} from '@wordpress/element';
import {withRouter} from "react-router-dom";

import Button from "../button";

class BackButton extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const {history, className, compact, ...props} = this.props;

		return (
			<Button
				secondary
				compact
				className={className}
				onClick={() => history.goBack()}>
				{this.props.children && this.props.children}
			</Button>
		)
	}
}

export default withRouter(BackButton)
