const defaults = {
	adminUrl: '',
	countries: [],
	currency: {
		code: 'USD',
		precision: 2,
		symbol: '$',
		symbolPosition: 'left',
		decimalSeparator: '.',
		thousandSeparator: ',',
	},
	siteTitle: '',
	wcAssetUrl: '',
};

const globalSharedSettings = typeof eAccountingi18n === 'object' ? eAccountingi18n : {};

// Use defaults or global settings, depending on what is set.
const allSettings = {
	...defaults,
	...globalSharedSettings,
};

allSettings.currency = {
	...defaults.currency,
	...allSettings.currency,
};

allSettings.locale = {
	...defaults.locale,
	...allSettings.locale,
};

export { allSettings };
