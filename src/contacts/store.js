import {registerStore} from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from "@wordpress/url";

const DEFAULT_STATE = {
	rows: [],
	total: 0,
	table: {
		orderby: 'id',
		order: 'desc',
		page: 1,
		per_page: parseInt(eAccountingi10n.per_page, 10),
		selected: [],
		filters: {},
	}
};

const actions = {
	setContacts(payload) {
		return {
			type: 'SET_CONTACTS',
			rows: [...payload]
		};
	},
	fetchFromAPI( path, params ) {
		return {
			type: 'FETCH_FROM_API',
			path,
			params,
		};
	},
};

const reducer = (state = DEFAULT_STATE, action) =>{
	switch (action.type) {
		case 'SET_CONTACTS':
			return { ...state, rows: action.rows};
	}
	return state;
};

const selectors = {
	getContacts(state, params){
		console.log(params);
		const {rows} = state;
		return rows;
	}
};


const controls = {
	FETCH_FROM_API( action) {
		return apiFetch( { path: addQueryArgs(action.path, action.params || {}) } );
	},
};


const resolvers = {
	* getContacts(data){
		const path = '/ea/v1/contacts/';
		const contacts = yield actions.fetchFromAPI( path, data );
		return actions.setContacts( contacts );
	}
};


const contacts = registerStore('eaccounting/contacts', {
	reducer,
	actions,
	selectors,
	controls,
	resolvers,
});
