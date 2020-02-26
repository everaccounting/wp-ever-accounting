const validFileTypes = [
	'text/csv',
	'text/plain',
	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	'application/vnd.ms-excel',
];

export const getFileExt = (fileName) => {
	const index = fileName.lastIndexOf('.');

	return index < 1 ? '' : fileName.substr(index + 1);
};

export const validateFileExt = (file) => {
	const ext = getFileExt(file.name);
	return 'csv' === ext && validFileTypes.indexOf(file.type) !== -1;
};

export const isValidRowItem = (index, item, headers) => {
	const colName = colRow[index] || false;

	if (false === colName) {
		return false;
	}

	if (validCols.indexOf(colName.trim()) !== -1) {
		return colName;
	}

	return false;
};

export const getValidRows = (rows, headers, validHeaders) => {
	const sanitizedRows = [];
	rows.forEach(row => {
		const sanitizedRow = {};
		row.forEach((rowItem, index) => {
			const head = headers[index] || false;
			if (head && (validHeaders.indexOf(head.trim()) !== -1)) {
				sanitizedRow[head] = rowItem;
			}
		});

		Object.keys(sanitizedRow).length && sanitizedRows.push(sanitizedRow);
	});
	return sanitizedRows;
};
