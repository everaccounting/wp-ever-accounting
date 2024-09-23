import Base from './base';
import Invoice from '../models/invoice';

export default Base.extend({
	endpoint: 'invoices',

	model: Invoice,
});
