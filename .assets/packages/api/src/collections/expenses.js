import Base from './base';
import Payment from '../models/payment';

export default Base.extend({
	endpoint: 'payments',

	model: Payment,
});
