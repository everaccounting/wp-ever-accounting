/**
 * WordPress dependencies
 */
import {Component, Fragment} from '@wordpress/element';
/**
 * Internal dependencies
 */
import SelectControl from '../select-control';
import {CONTACT_TYPES} from '@eaccounting/data';

export default class ContactTypesControl extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Fragment>
				<SelectControl options={CONTACT_TYPES} {...this.props} />
			</Fragment>
		);
	}
}


