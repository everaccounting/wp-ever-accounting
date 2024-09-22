import Base from './base';
import Item from '../models/item';

export default Base.extend({
	endpoint: 'items',

	model: Item,
});
