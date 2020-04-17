import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import {withRouter} from "react-router-dom";
import {__} from '@wordpress/i18n';

import Button from "../button";

class BackButton extends Component {
	static propTypes = {
		path: PropTypes.string,
		title: PropTypes.string,
	};

	static defaultProps = {
		title: __('Back'),
	};

	constructor(props) {
		super(props);
	}

	render() {
		const {title, path = null, history, ...props} = this.props;

		return (
			<Button secondary onClick={() => history.goBack()}>{title}</Button>
		)
	}
}

export default withRouter(BackButton)
