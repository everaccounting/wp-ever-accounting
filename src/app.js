/**
 * External dependencies
 */
import React from 'react';
// import {select} from '@wordpress/data-controls';
import {select} from '@wordpress/data';
/**
 * Internal dependencies
 */
import {COLLECTIONS_STORE_KEY, SCHEMA_STORE_KEY} from "data";
import Table from './table';

class App extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			page:1
		};
	}

	onClick = () => {
		const {page} = this.state;
		this.setState({
			page:page+1
		});
		select(COLLECTIONS_STORE_KEY).getCollection('/ea/v1', 'contacts', {page});
	};

	render() {
		const {page} = this.state;
		const contacts = select(COLLECTIONS_STORE_KEY).getCollection('/ea/v1', 'contacts', {page});
		const { isResolving, hasFinishedResolution } = select(
			SCHEMA_STORE_KEY
		);

		return (
			<div>
				<button onClick={this.onClick}>CLICK</button>
				<ul>
					{contacts.map(contact => {
						return (<li key={contact.id}>{contact.address}</li>)
					})}
				</ul>

				<Table/>
			</div>
		);
	}
}

export default App;
