import Base from './base';
import Note from '../models/note';

export default Base.extend({
	endpoint: 'notes',

	model: Note,
});
