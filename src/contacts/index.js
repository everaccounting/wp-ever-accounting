import {Component} from "react";
import { compose, withState } from '@wordpress/compose';
import { withSelect, withDispatch, useSelect } from '@wordpress/data';


class Contacts extends Component{

	constructor(props, context) {
		super(props, context);
	}

	componentDidMount() {
		this.props.fetchFromAPI('/ea/v1/contacts/');
		// useSelect('eaccounting/contacts').getContacts({});
	}

	fetchContacts = ()=> {

	}


	render() {
		return (
			<div>
				Hello
				{/*<button onClick={fetchContacts}>Hello</button>*/}
			</div>
		)
	}
}

export default compose( [
	withSelect((select )=> ({
		contacts:select('eaccounting/contacts').getContacts,
	})),
	withDispatch( ( dispatch ) => ( {
		fetchFromAPI: dispatch( 'eaccounting/contacts' ).fetchFromAPI,
	} ) ),
])(Contacts);
