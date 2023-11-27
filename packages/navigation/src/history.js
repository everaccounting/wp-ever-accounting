//https://jasonwatmore.com/react-router-6-navigate-outside-react-components
let _history;
function getHistory() {
	if ( ! _history ) {
		_history = {
			navigate: null,
			location: null,
			params: null,
		};
	}
	return _history;
}

export { getHistory };
