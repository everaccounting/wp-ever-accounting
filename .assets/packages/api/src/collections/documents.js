import Base from './base';
import Document from '../models/document';

export default Base.extend({
	endpoint: 'documents',

	model: Document,
});
