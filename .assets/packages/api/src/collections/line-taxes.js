import Base from './base';
import LineTax from '../models/line-tax';

export default Base.extend({
	endpoint: 'line-taxes',

	model: LineTax,
});
