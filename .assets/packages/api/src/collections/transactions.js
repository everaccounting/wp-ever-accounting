import Base from './base';
import Transaction from '../models/transaction';

export default Base.extend({
	endpoint: 'transactions',

	model: Transaction,
});
