/**
 * Internal dependencies
 */
import { STORE_NAME } from '../constants';
export const getSelectors = (options) => {
	const { select, name, query = {}, id = undefined } = options;
	const {
		getSchema,
		getEntityRecords,
		getTotalEntityRecords,
		getEntityFetchError,
		getEntityRecord,
		isSavingEntityRecord,
		isDeletingEntityRecord,
		getEntityRecordSaveError,
		getEntityRecordDeleteError,
		getOptions,
		getDefaultCurrency,
		getDefaultAccount,
		isResolving,
	} = select(STORE_NAME);

	let response = {
		schema: getSchema(name),
		item: undefined,
		items: [],
		total: 0,
		isRequestingItems: false,
		isRequestingItem: false,
		isRequesting: false,
		isSavingItem: isSavingEntityRecord(name, id),
		isDeletingItem: isDeletingEntityRecord(name, id),
		itemsFetchError: {},
		itemFetchError: {},
		itemSaveError: getEntityRecordSaveError(name, id),
		itemDeleteError: getEntityRecordDeleteError(name, id),
		options: getOptions(),
		defaultCurrency: getDefaultCurrency(),
		defaultAccount: getDefaultAccount(),
	};

	if (name && !id) {
		response = {
			...response,
			...{ items: getEntityRecords(name, query) },
			...{ total: getTotalEntityRecords(name, query) },
			...{ itemsFetchError: getEntityFetchError(name, query) },
			...{
				isRequestingItems:
					isResolving('getEntityRecords', [name, query]) === true,
			},
		};
	}

	if (!!name && !!id) {
		response = {
			...response,
			...{ item: getEntityRecord(name, id, query) },
			...{
				itemFetchError: getEntityFetchError(name, query, id),
			},
			...{
				isRequestingItem:
					isResolving('getEntityRecord', [name, query, id]) === true,
			},
		};
	}

	response = {
		...response,
		...{
			isRequesting:
				response.isRequestingItems || response.isRequestingItem,
		},
	};

	return response;
};

/**
 * Get Item schema based on resource name.
 */
export const getItems = (options) => {
	const { select, name, query = {} } = options;
	if (!name) {
		return {};
	}
	const {
		getEntityRecords,
		getTotalEntityRecords,
		getEntityFetchError,
		isResolving,
	} = select(STORE_NAME);
	const response = {
		items: [],
		total: 0,
		isRequesting: false,
		error: {},
		isError: false,
		errorMessage: '',
	};

	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const items = getEntityRecords(name, query);
	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const total = getTotalEntityRecords(name, query);
	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const error = getEntityFetchError(name, query);
	if (isResolving('getEntityRecords', [name, query])) {
		return { ...response, isRequesting: true };
	}

	return {
		...response,
		items,
		total,
		error,
		isError: !!error,
		errorMessage: error ? error.message : '',
	};
};

export const getItem = (options) => {
	const { select, name, id, query = {} } = options;
	const {
		getEntityRecord,
		getEntityFetchError,
		isSavingEntityRecord,
		isDeletingEntityRecord,
		getEntityRecordDeleteError,
		getEntityRecordSaveError,
		isResolving,
	} = select(STORE_NAME);
	const response = {
		item: {},
		isRequesting: false,
		isSaving: false,
		isDeleting: false,
		fetchError: {},
		saveError: {},
		deleteError: {},
	};
	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const item = id && getEntityRecord(name, id, query);
	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const fetchError = id && getEntityFetchError(name, query, id);
	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const isSaving = isSavingEntityRecord(name, id);
	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const isDeleting = isDeletingEntityRecord(name, id);
	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const saveError = getEntityRecordSaveError(name, id);
	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const deleteError = getEntityRecordDeleteError(name, id);
	if (isResolving('getEntityRecord', [name, id, query])) {
		return { ...response, isRequesting: true };
	}
	return {
		...response,
		item,
		isSaving,
		isDeleting,
		fetchError,
		saveError,
		deleteError,
	};
};

/**
 * Returns items based on a search query.
 *
 * @param  {Object}   select    Instance of @wordpress/select
 * @param  {string}   name  Report API Endpoint
 * @param  {string[]} search    Array of search strings.
 * @return {Object}   Object containing API request information and the matching items.
 */
export function searchItemsByString(select, name, search) {
	const { getEntityRecords, getEntityFetchError, isResolving } =
		select(STORE_NAME);

	const items = {};
	let isRequesting = false;
	let isError = false;
	search.forEach((searchWord) => {
		const query = {
			search: searchWord,
			per_page: 10,
		};
		const newItems = getEntityRecords(name, query);
		newItems.forEach((item, id) => {
			items[id] = item;
		});
		if (isResolving('getEntityRecords', [name, query])) {
			isRequesting = true;
		}
		if (getEntityFetchError(name, query)) {
			isError = true;
		}
	});

	return { items, isRequesting, isError };
}
