function useCurrencies() {
	const { currencies, isLoading, isError } = useSelect( ( select ) => {
		const { getCurrencies, getCurrenciesError } = select( STORE_NAME );
		return {
			currencies: getCurrencies(),
			isLoading: ! getCurrencies.isResolving(),
			isError: !! getCurrenciesError(),
		};
	}, [] );
	return {
		currencies,
		isLoading,
		isError,
		getCurrencies,
	};
}

export default useCurrencies;
