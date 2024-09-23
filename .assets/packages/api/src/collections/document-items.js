import Base from './base';
import DocumentItem from '../models/document-item';

export default Base.extend({
	model: DocumentItem,

	/**
	 * Update Amounts
	 *
	 * @return {void}
	 */
	updateAmounts() {
		this.each(item => item.updateAmounts());
	}
});
