import Base from './base';
import Bill from '../models/bill';

export default Base.extend({
	endpoint: 'bills',

	model: Bill,
});
