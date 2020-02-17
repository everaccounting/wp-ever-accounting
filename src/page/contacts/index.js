/**
 * External dependencies
 */

import {Component, Fragment} from 'react';
import { translate as __ } from 'lib/locale';


/**
 * Internal dependencies
 */
import './style.scss';

export default class Contacts extends Component {
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
		return(
			<Fragment>
				<h1 className="wp-heading-inline">{__('Contacts')}</h1>
			</Fragment>
		)
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
// )( Contacts );
