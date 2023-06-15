/**
 * External dependencies
 */
const { createBrowserHistory } = require('history');
const { parse } = require('qs');

let _history;

function getHistory() {
	if (!_history) {
		const browserHistory = createBrowserHistory();
		_history = {
			get action() {
				return browserHistory.action;
			},
			get location() {
				const { location } = browserHistory;
				const query = parse(location.search.substring(1));
				let pathname;

				if (query && typeof query.path === 'string') {
					pathname = query.path;
				} else if (
					query &&
					query.path &&
					typeof query.path !== 'string'
				) {
					console.warn(
						`Query path parameter should be a string but instead was: ${query.path}, undefined behaviour may occur.`
					);
					pathname = query.path;
				} else {
					pathname = '/';
				}
				return {
					...location,
					pathname,
				};
			},
			createHref: browserHistory.createHref,
			push: browserHistory.push,
			replace: browserHistory.replace,
			go: browserHistory.go,
			back: browserHistory.back,
			forward: browserHistory.forward,
			block: browserHistory.block,
			listen(listener) {
				return browserHistory.listen(() => {
					listener({
						action: this.action,
						location: this.location,
					});
				});
			},
		};
	}
	return _history;
}

export { getHistory };
