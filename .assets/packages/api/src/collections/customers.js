import Base from './base';
import Customer from '../models/customer';

export default Base.extend({
	endpoint: 'customers',

	model: Customer,
});
