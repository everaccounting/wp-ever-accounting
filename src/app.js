/**
 * External dependencies
 */
import React from 'react';
// import {select} from '@wordpress/data-controls';
import {select} from '@wordpress/data';
import { withDispatch, withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
/**
 * Internal dependencies
 */
import {COLLECTIONS_STORE_KEY, SCHEMA_STORE_KEY} from "data";
import Table from './table';
import store from './store';
import {withSpokenMessages} from "@wordpress/components";

class App extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			page:1
		};
		//props.fetchFromAPI('/wp/v2/posts/1')
	}

	onClick = () => {
		const {page} = this.state;
		this.setState({
			page:page+1
		});
		// select(COLLECTIONS_STORE_KEY).getCollection('/ea/v1', 'contacts', {page});
	};

	render() {
		// const { isResolving, hasFinishedResolution } = select(
		// 	SCHEMA_STORE_KEY
		// );
		// console.log(isResolving());
		// console.log(hasFinishedResolution());
		// const {page} = this.state;
		// const contacts = select(COLLECTIONS_STORE_KEY).getCollection('/ea/v1', 'contacts', {page});
		// const post = this.props.post(1);
		// console.log(hasFinishedResolution());

		console.log(this.props.post);
		const {post} = this.props;
		return (
			<div>
				<button onClick={this.onClick}>CLICK</button>
				{post && post.modified && post.modified}
				<ul>
					{/*{contacts.map(contact => {*/}
					{/*	return (<li key={contact.id}>{contact.address}</li>)*/}
					{/*})}*/}
				</ul>

				<Table path={'contacts'}/>
				<Table path={'transaction'}/>
				<Table/>
			</div>
		);
	}
}

// export default App;
export default compose(
	withSelect(select => {
		const post = select('demostore').getPost(1)
		return {
			post
		}
	}),
	withDispatch(dispatch => {
		return {
			setPost : dispatch('demostore').setPost
		}
	})
	// withSelect(  select => {
	// 	const { getPost } = select( 'demostore' ).getPost;
	// 	return {
	// 		post: getPost,
	// 	};
	// } ),
	// withDispatch( ( dispatch ) => {
	// 	const { fetchFromAPI } = dispatch( 'demostore' );
	// 	return {
	// 		fetchFromAPI,
	// 	};
	// } )
)( App );
