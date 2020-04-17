/**
 * WordPress dependencies
 */
import {Component, Fragment} from '@wordpress/element';
/**
 * Internal dependencies
 */
import SelectControl from '../select-control';
import {CATEGORY_TYPES} from '@eaccounting/data';

export default class CategoryTypesControl extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Fragment>
				<SelectControl options={CATEGORY_TYPES} {...this.props} />
			</Fragment>
		);
	}
}


