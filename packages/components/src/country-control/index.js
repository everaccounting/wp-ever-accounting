/**
 * WordPress dependencies
 */
import {Component, Fragment} from '@wordpress/element';
/**
 * Internal dependencies
 */
import SelectControl from '../select-control';
import {COUNTRIES} from '@eaccounting/data';

export default class CountryControl extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Fragment>
				<SelectControl options={COUNTRIES} {...this.props} />
			</Fragment>
		);
	}
}


