import Base from './base';
import Contact from '../models/contact';

export default Base.extend({
	endpoint: 'contacts',

	model: Contact,
});
