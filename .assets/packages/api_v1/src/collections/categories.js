import Base from './base';
import Category from '../models/category';

export default Base.extend({
	endpoint: 'categories',

	model: Category,
});
