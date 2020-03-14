/**
 * External dependencies
 */
import { Component } from 'react';

/**
 * Internal dependencies
 */
import Card from '../card';

export default class CompactCard extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return <Card {...this.props} compact />;
	}
}
