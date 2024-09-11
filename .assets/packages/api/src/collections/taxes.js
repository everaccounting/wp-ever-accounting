import Base from './base';
import Tax from '../models/tax';

export default Base.extend({
	endpoint: 'taxes',

	model: Tax,
});
