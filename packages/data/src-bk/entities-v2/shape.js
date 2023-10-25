// reducers data shape.

const reducer = {
	config: {},
	entities: {
		accounts: {
			items: [], // array of account objects
			queries: {
				'': {
					items: [], // array of account objects keys.
					count: 0, // total count of accounts.
					pending: false,
					error: null,
				},
			},
			saving: {
				[ accountId ]: {
					pending: false,
					error: null,
				},
			},
		},
	},
};
