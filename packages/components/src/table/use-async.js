export default function useAsync({
	fetchData: propsFetchData,
	data: propsData,
	isLoading: propsIsLoading = false,
	query: propsQuery,
	onQueryChange: propsOnQueryChange,
	cacheOptions = false,
	filterQuery = null,
	...restSelectProps
}) {}
