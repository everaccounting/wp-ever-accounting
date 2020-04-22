/**
 * WordPress dependencies
 */
import { Component, Fragment } from '@wordpress/element';
/**
 * Internal dependencies
 */
import SelectControl from '../select-control';
/**
 * External dependencies
 */
import { COUNTRIES } from '@eaccounting/data';

export default class CountrySelect extends Component {
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
