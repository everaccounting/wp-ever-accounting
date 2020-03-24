/**
 * WordPress dependencies
 */
import {Component, Fragment} from '@wordpress/element';
/**
 * Internal dependencies
 */
import SelectControl from '../select-control';
import {withSelect} from '@wordpress/data';

class ContactTypesControl extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Fragment>
				<SelectControl options={this.props.options} {...this.props} />
			</Fragment>
		);
	}
}

export default withSelect(select => {
	return {
		options: select('ea/store').getCollection('contacts/types')
	}
})(ContactTypesControl)


