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
import { PAYMENT_METHODS } from '@eaccounting/data';

export default class paymentMethodSelect extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Fragment>
				<SelectControl options={PAYMENT_METHODS} {...this.props} />
			</Fragment>
		);
	}
}
