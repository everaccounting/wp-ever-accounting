/**
 * List Table
 *
 * This is a WordPress WP_List_Table ported to Jquery.
 *
 * @package EverAccounting
 * @since 1.0.0
 * @version 1.0.0
 */

(function($, window, document, undefined) {
	'use strict';

	/**
	 * List Table
	 *
	 * This is a WordPress WP_List_Table ported to Jquery.
	 *
	 * @package EverAccounting
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	function ListTable(element, options) {
		this.$element = $(element);
		this.options = $.extend({}, ListTable.DEFAULTS, options);
		this.init();
	}

	ListTable.DEFAULTS = {
		className: '',
		columns: [],
		query: {},
		bulkActions: [],
		onBulkAction: (action, selected, cb) => {},
		onQueryChange: (query, listTable, cb) => {},
	};

	ListTable.prototype.init = function() {
		var _this = this;
		var $element = this.$element;
		var options = this.options;
	}

	$.fn.listTable = function(option) {
		return this.each(function() {
			var $this = $(this);
			var data = $this.data('listTable');
			var options = typeof option === 'object' && option;

			if (!data) {
				$this.data('listTable', (data = new ListTable(this, options)));
			}

			if (typeof option === 'string') {
				data[option]();
			}
		});
	}

	$.fn.listTable.Constructor = ListTable;
}(jQuery, window, document));


