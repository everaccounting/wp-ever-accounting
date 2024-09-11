import Base from './base';
import LineItem from '../models/line-item';

export default Base.extend({
	endpoint: 'line-items',

	model: LineItem,
});
