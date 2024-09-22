import Base from './base';
import Currency from '../models/currency';

export default Base.extend({
	endpoint: 'currencies',

	model: Currency,
});
