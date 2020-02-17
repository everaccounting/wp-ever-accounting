/**
 * External dependencies
 */

import {Component} from 'react';
import { translate as __ } from 'lib/locale';


/**
 * Internal dependencies
 */
import './style.scss';

export default class Bills extends Component {
	constructor( props ) {
		super(props);
		this.state = {};
		window.addEventListener( 'popstate', this.onPageChanged );
	}

	componentDidCatch( error, info ) {
		this.setState( { error: true, stack: error, info } );
	}

	componentWillUnmount() {
		window.removeEventListener( 'popstate', this.onPageChanged );
	}


	render() {
		return null;
	}
}

// function mapDispatchToProps( dispatch ) {
// 	return {}
// }
// function mapStateToProps( state ) {
// 	return {}
// }
//
// export default connect(
// 	mapStateToProps,
// 	mapDispatchToProps,
// )( Bills );
