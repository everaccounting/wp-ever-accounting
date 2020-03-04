import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
export default class Items extends Component {
	constructor( props ) {
		super(props);
		this.state = {};
	}

	componentDidCatch( error, info ) {
		this.setState( { error: true, stack: error, info } );
	}


	render() {
		return(
			<Fragment>
				<h1 className="wp-heading-inline">{__('Items')}</h1>
			</Fragment>
		)
	}
}
