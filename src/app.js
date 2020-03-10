/**
 * External dependencies
 */
import React from 'react';
// import {select} from '@wordpress/data-controls';
import {withDispatch, withSelect} from '@wordpress/data';
import {compose} from '@wordpress/compose';
/**
 * Internal dependencies
 */

import Table from './table';
import store from './demostore';


class App extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			page:1
		}
	}

	onClick = () => {
		const {page} = this.state;
		this.setState({
			page:page+1
		});
		//this.props.getAccounts('/ea/v1', 'account', {page:page})
		// select(COLLECTIONS_STORE_KEY).getCollection('/ea/v1', 'contacts', {page});
	};

	render() {
		const {post, accounts = [] } = this.props;
		console.log(accounts);
		return (
			<div>
				<button onClick={this.onClick}>CLICK</button>
				<br/>
				<Table resourceName={'contacts'}/>
				<br/>
				{/*<ul>*/}
				{/*	{accounts.map((account)=> {*/}
				{/*		return(<li key={account.id}>{account.address}</li>)*/}
				{/*	})}*/}
				{/*</ul>*/}
				<br/>
				{/*{post && post.content && post.content.rendered}*/}
			</div>
		);
	}
}

export default App;

