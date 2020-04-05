export const normalizeResponseBody = (response, callback = x => x) => {
	const {response: responses} = response;

	if( !Array.isArray(responses))
		return null;

	let merge = [];

	responses.forEach(response => {
		if(Array.isArray(response.body))
			merge = [...merge, ...response.body];
		else if(typeof response.body === 'string')
			merge = [...merge, response.body];
		else
			merge = [...merge, {...response.body}];
	});

	// if(typeof callback === 'function'){
		console.log(callback);
		merge = merge.map( v => {
			return callback(v);
		});
	// }

	return merge;
};
