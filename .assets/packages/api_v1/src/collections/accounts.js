import Base from './base';
import Account from '../models/account';

export default Base.extend({
	endpoint: 'accounts',

	model: Account,
});
