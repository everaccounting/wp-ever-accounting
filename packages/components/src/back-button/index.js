/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
/**
 * External dependencies
 */
import { withRouter } from 'react-router-dom';

/**
 * Internal dependencies
 */
import Button from '../button';

class BackButton extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const { history, className, compact = false, ...props } = this.props;

		return (
			<Button secondary compact={compact} className={className} onClick={() => history.goBack()}>
				{this.props.children && this.props.children}
			</Button>
		);
	}
}

export default withRouter(BackButton);
