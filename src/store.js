import apiFetch from '@wordpress/api-fetch';
import { registerStore } from "@wordpress/data";

const DEFAULT_STATE = {
	posts: {},
	discountPercent: 0,
};

const actions = {
	setPost( item, post ) {
		return {
			type: 'SET_PRICE',
			item,
			post,
		};
	},

	startSale( discountPercent ) {
		return {
			type: 'START_SALE',
			discountPercent,
		};
	},

	fetchFromAPI( path ) {
		return {
			type: 'FETCH_FROM_API',
			path,
		};
	},
};

registerStore( 'demostore', {
	reducer( state = DEFAULT_STATE, action ) {
		switch ( action.type ) {
			case 'SET_PRICE':
				return {
					...state,
					posts: {
						...state.posts,
						[ action.item ]: action.post,
					},
				};

			case 'START_SALE':
				return {
					...state,
					discountPercent: action.discountPercent,
				};
		}

		return state;
	},

	actions,

	selectors: {
		getPost( state, item ) {
			const { posts } = state;
			return posts[ item ];
		},
	},

	controls: {
		FETCH_FROM_API( action ) {
			console.log(action.path);
			return apiFetch( { path: action.path } )
		},
	},

	resolvers: {
		* getPost( item ) {
			const path = '/wp/v2/posts/' + item;
			const post = yield actions.fetchFromAPI( path );
			return actions.setPost( item, post );
		},
	},
} );
