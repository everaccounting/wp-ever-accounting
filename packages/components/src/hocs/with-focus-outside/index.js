/**
 * WordPress dependencies
 */
import { withFocusOutside as withFocusOutsideComponent } from '@wordpress/components';
import { Component } from '@wordpress/element';

const withFocusOutside = withFocusOutsideComponent(
	class extends Component {
		handleFocusOutside(event) {
			this.props.onFocusOutside(event);
		}

		render() {
			return this.props.children;
		}
	}
);

export default withFocusOutside;
